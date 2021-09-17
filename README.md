# ReadyMage Maintenance

Magento 2 module which allows `maintenance:disable` command to preserve flag file symlinks. 
Most common use case is for distributed infrastructure with multiple replicas where php-cli user from single replica can trigger maintenance mode which will be propagated trough other replicas.


## Attention

This module only preserves symlinks. It will not create them from scratch if they did not exist prior. The initial symlink creation must be handled by CI/CD systems or manual actions.

## Installation

Add to any Magento 2 project with following commands:

  ```shell
  composer require readymage/maintenance --no-interaction --update-no-dev --prefer-dist
  php bin/magento module:enable ReadyMage_Maintenance
  ```
