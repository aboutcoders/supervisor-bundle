Configuration Reference
=======================

```yaml
# app/config/config.yml
abc_supervisor:
    connections:
        localhost:                  # Configure connections accordding to the supervisor inet_http_server configuration
            host:                   # The host name (e.g. localhost or supervisor.domain.tld)
            port: 9001              # The port number
            username: user          # The username
            password: secret        # The password
```