<?php

namespace App\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class SonataController extends CRUDController implements ServiceSubscriberInterface
{
    // TODO add locking reource by user
}
