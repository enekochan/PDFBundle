BushidoIOPDFBundle
==================

The BushidoIOPDFBundle adds PDF file creation support in Symfony2.

Features included:

- PDF creation from HTML string content
- `Symfony\Component\HttpFoundation\Response` object encapsulation with
`application/pdf` content type
- Temporal data and fonts paths can be located inside or outside Symfony2 app folder tree

Installation
------------
### Step 1: Composer
Add the following require line to the `composer.json` file:
``` json
{
    "require": {
        "bushidoio/pdf-bundle": "dev-master"
    }
}
```
And actually install it in your project using Composer:
``` bash
php composer.phar install
```
You can also do this in one step with this command:
``` bash
$ php composer.phar require bushidoio/pdf-bundle "dev-master"
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new BushidoIO\PDFBundle\BushidoIOPDFBundle(),
    );
}
```

Configuration
-------------
Temporal content folders can be configured in `app/config/config.yml`. By
default both tmp and ttffontdatapath folder will be stored in `app/cache`.
Be sure you have write permissions on both folders. 
``` yaml
bushidoio_pdf:
    tmp: ~
    ttffontdatapath: ~
```
Usage examples
--------------
You can transform any HTML string to PDF with the `bushidoio_pdf` service:
``` php
public function indexAction()
{
    ...
    $PDFService = $this->get('bushidoio_pdf');
    $html = '...';
    $pdf = $PDFService->createPDFFromHtml($html);
    ...
}
```
You can use Twig templates, or anything you like, to create the HTML string:
``` php
public function indexAction()
{
    ...
    $PDFService = $this->get('bushidoio_pdf');
    $html = $this->get('twig')->render(
        'default/index.html.twig',
        array(
            'greeting' => 'Hi'
        )
    );
    $pdf = $PDFService->createPDFFromHtml($html);
    ...
}
```
With the `createResponse` method a `Symfony\Component\HttpFoundation\Response`
object is returned with `application/pdf` content type that will be directly
downloaded if returned in a controller action:
``` php
public function indexAction()
{
    $PDFService = $this->get('bushidoio_pdf');
    $html = $this->get('twig')->render(
        'default/index.html.twig',
        array(
            'greeting' => 'Hi'
        )
    );
    
    return $PDFService->createResponse($html);
}
```

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

This bundle uses mPDF under the hood. mPDF is a PHP class to generate PDF files
from HTML with Unicode/UTF-8 and CJK support by Ian Back and it's released under
the GPL license.
