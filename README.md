# MMCmfContentBundle

### Append to app/AppKernel.php
You need to add the bundle to your app/AppKernel.php.

```
...
    public function registerBundles()
    {
        $bundles = array(
            ...
            new MandarinMedien\MMCmfContentBundle\MMCmfContentBundle(),
            ...
            );
    ....
    }
...
```

### Append to app/config/config.yml

```
...
imports:
    - ...
    # Symfony 2
    - { resource: '@MMCmfContentBundle/Resources/config/config.yml' }
    #   OR    
    # Symfony 3
    - { resource: '@MMCmfContentBundle/Resources/config/config_symfony3.yml' }
    - ...   
...
```


### Config -> app/config/config.yml
```
...
mm_cmf_content:
    content_nodes:
        contentNode:
            templates:
              - { name: 'default' }
              - { name: 'tile' }
        myCustomContentNode:
            templates:
              - { name: 'default' }
...
```

### install and initiate assets - Symfony 2

```
...
shell:PROJECT_ROOT: cd  vendor/mandarinmedien/mmcmfcontentbundle/MandarinMedien/MMCmfContentBundle && bower update && cd ../../../../../ && app/console as:in --symlink && app/console assetic:dump
...
```

### install and initiate assets - Symfony 3

```
...
shell:PROJECT_ROOT: cd  vendor/mandarinmedien/mmcmfcontentbundle/MandarinMedien/MMCmfContentBundle && bower update && cd ../../../../../ && bin/console as:in --symlink && bin/console assetic:dump
...
```