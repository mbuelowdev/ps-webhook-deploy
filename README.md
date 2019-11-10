# ps-webhook-deployment
A tool to automatically deploy GitHub projects via GitHub webhooks on a webserver.

### Dependencies
 - PHP 7.2 or higher
 - Git (globally installed)
 - Composer (if you want to deploy composer projects)
 
### Deployment Configuration
Example of a configuration file. 
```
version: 1
deployment:
    visibility: 'public'
    link: 'public'
    secrets:
        database_url: '.env'
        auth_basic_username: 'config/services.yaml'
        auth_basic_password: 'config/services.yaml'
``` 
##### Required fields:
- *version*: Version of config file format. (1 = current)
- *visibility*:
    - *public* = reachable from the internet
    - *private* = reachable only from localhost
##### Optional fields:
- *link*: The name of the directory in project root to be linked. Defaults to project root if none is set.   
- *secrets*: List of placeholders and in which files there are to be found. You have to inject the secrets through the */secrets/add* endpoint first. Placeholders have to look like this to be replaced on deployment: ({placeholder_name})

### Endpoints
A better description can be found in the *swagger.json* file.

| Method | Path                  | Auth           | Description                                         |
|--------|-----------------------|----------------|-----------------------------------------------------|
| GET    | /doc                  | -              | Returns the *swagger.json* of the api.              |
| POST   | /deploy               | whitelist.json | Default GitHub webhook endpoint.                    |
| GET    | /deployments          | basic          | List all deployed projects (public and private).    |
| POST   | /deployments/undeploy | basic          | Undeploy a specific deployment.                     |
| POST   | /deployments/link     | basic          | Link a specific deployment.                         |
| POST   | /deployments/unlink   | basic          | Unlink a specific deployment.                       |
| POST   | /secrets/add          | basic          | Add secrets to a deployment.                        |
| POST   | /secrets/purge        | basic          | Remove all secrets from a deployment.               |

### Other
For frontend projects that need a public path to be set use a relative one. For example in a vue project:

1. Create vue.config.json in project root.
2. Add the publicPath variable and set it to './'