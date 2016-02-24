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

### install and initiate assets

```
...
shell:PROJECT_ROOT: cd vendor/mandarinmedien/mmcmfadminbundle/MandarinMedien/MMCmfContentBundle && bower update && cd ../../../../../ && app/console as:in --symlink && app/console assetic:dump
...
```