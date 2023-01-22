<?php

namespace App\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class SonataController extends CRUDController implements ServiceSubscriberInterface
{
    //TODO add locking reource by user
}