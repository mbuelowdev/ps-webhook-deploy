<?php
/**
 * @author 42Pollux
 * @since 2019-11-05
 */

namespace App\Deployment;


use App\Logger\Logger;
use App\Repository\SecretRepository;

class Inserter
{
    /**
     * @var SecretRepository
     */
    private $secretRepository;

    /**
     * Inserter constructor.
     * @param SecretRepository $secretRepository
     */
    public function __construct(SecretRepository $secretRepository)
    {
        $this->secretRepository = $secretRepository;
    }

    /**
     * @param string $deploymentName
     * @return bool
     */
    public function hasInsertions(string $deploymentName) : bool
    {
        $secretEntries = $this->secretRepository->findBy(array(
            'deployment_name' => $deploymentName
        ));

        if (!empty($secretEntries)) {
            return true;
        } else {
            return false;
        }
    }

    public function insertSecrets(string $installationPath, array $secrets)
    {
        $secretCount = 0;
        $deploymentName = basename($installationPath);

        Logger::info('Inserting ' . count($secrets) . ' secret(s).');

        // For every file mentioned in the psdeploy.yaml secret section
        // $secret[0] = name of the secret placeholder
        // $secret[1] = name of the file where secrets should be inserted
        foreach ($secrets as $secret) {
            $secretFile = $installationPath . '/' . $secret[1];
            if (file_exists($secretFile)) {
                // Find secret in database
                $secret = $this->secretRepository->getSecretByName($deploymentName, $secret[0]);
                $secretIdentifier = '({' . $secret->getSecretKey() . '})';

                // Load the file
                $content = file_get_contents($secretFile);

                // Replace any occourences of the placeholder with its secret value
                $content = str_replace($secretIdentifier,  $secret->getSecretValue(), $content);

                // Save back to file
                file_put_contents($secretFile, $content);

                $secretCount++;
            } else {
                Logger::warning('File ' . $secretFile . ' not found for secret injection.');
            }
        }

        Logger::info('Successfully inserted ' . $secretCount . ' secrets.');
    }


}