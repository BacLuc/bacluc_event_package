<?php

namespace Concrete\Package\BaclucEventPackage;

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Package\Package;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class Controller extends Package
{
    protected $pkgHandle               = 'bacluc_event_package';
    protected $appVersionRequired      = '5.7.4';
    protected $pkgVersion              = '0.0.1';
    protected $pkgAutoloaderRegistries = array(
        'src' => 'BaclucEventPackage'
    );

    public function getPackageName()
    {
        return t("BaclucEventPackage");
    }

    public function getPackageDescription()
    {
        return t("Package to Manage Events");
    }

    public function install()
    {
        $em = $this->app->make(EntityManagerInterface::class);
        //begin transaction, so when block install fails, but parent::install was successfully, you don't have to uninstall the package
        $em->getConnection()->beginTransaction();
        try {
            $pkg = parent::install();
            //add blocktypeset
            if (!Set::getByHandle('bacluc_event_set')) {
                Set::add('bacluc_event_set', 'Appointment', $pkg);
            }
            BlockType::installBlockType("bacluc_event_block", $pkg);
            Set::getByHandle('bacluc_event_set')->addBlockType(BlockType::getByHandle("bacluc_event_block"));
            BlockType::installBlockType("bacluc_next_event_block", $pkg);
            Set::getByHandle('bacluc_event_set')->addBlockType(BlockType::getByHandle("bacluc_next_event_block"));
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function uninstall()
    {
        $eventblock = BlockType::getByHandle("bacluc_event_block");
        $em = $this->app->make(EntityManagerInterface::class);
        $em->getConnection()->beginTransaction();
        try {

            if (is_object($eventblock)) {
                $eventblock->delete();
            }
            parent::uninstall();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function getPackageDependencies()
    {
        return [
            "bacluc_c5_crud" => true
        ];
    }


}