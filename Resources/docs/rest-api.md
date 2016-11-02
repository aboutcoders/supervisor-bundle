REST-API
========

The AbcSupervisorBundle ships with a JSON REST-API. To use this you need to make sure the following bundles are installed and configured:
 
* [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)

Next you need to make sure that the routing files are imported:

```yaml
# app/config/routing.yml
abc-rest-job:
    type: rest
    resource: "@AbcSupervisorBundle/Resources/config/routing/rest-all.xml"
    prefix: /api
```

You can see an overview of all available API methods using the API documentation provided by the [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle).