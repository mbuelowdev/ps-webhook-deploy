<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Helper;


use App\Logger\Logger;

class Shell
{
    /**
     * Wraps around the standard library "exec" command to throw
     * exceptions in case of errors.
     *
     * @param string $command
     * @throws \Exception
     */
    public static function exec(string $command)
    {
        $content = array();
        $code = -1;

        // Log the command
        Logger::debug('Executing command: ' . $command);

        // Execute command
        exec($command . ' 2>&1', $content, $code);

        $content = implode(' ### ', $content);

        // Throw an exception in case it wasn't successfull
        if ($code !== 0) {
            throw new \Exception(
                Texts::EXCEPTION_SHELL_COMMAND_FAILED . " ($code) ($content)",
                500
            );
        }
    }
}