<?php
/**
 * @author 42Pollux
 * @since 2019-11-01
 */

namespace App\Helper;


class Texts
{
    const EXCEPTION_FAILED_TO_DESERIALIZE_GITHUB_RESPONSE = 'Failed to deserialize Github JSON response.';
    const EXCEPTION_FAILED_TO_PERSIST = 'Failed to persist database entity.';
    const EXCEPTION_FAILED_TO_REMOVE = 'Failed to remove database entity';
    const LOG_WORKER_START = 'Started deployment worker.';
    const EXCEPTION_SHELL_COMMAND_FAILED = 'Failed to execute a shell command.';
    const EXCEPTION_FAILED_TO_PROCESS_JOB = 'Failed to process job.';
    const EXCEPTION_FAILED_TO_PARSE_CONFIG = 'Failed to parse psdeploy yaml configuration.';
    const EXCEPTION_FAILED_TO_VALIDATE_YAML = 'Failed to validate the psdeploy yaml configuration.';
    const EXCEPTION_MISSING_PARAMETERS = 'Missing required parameters in the service.yaml file.';
    const LOG_NO_PSDEPLOY_FILE_FOUND = 'No psdeploy yaml file found. Aborting deployment...';
    const EXCEPTION_FAILED_TO_DESERIALIZE_JSON = 'Failed to deserialize json.';
    const EXCEPTION_FAILED_TO_VALIDATE_JSON = 'Failed to validate json.';
    const EXCEPTION_NEARLY_DELETED_FS = 'You nearly deleted the whole filesystem because of missing configuration';
}