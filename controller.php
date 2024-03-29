<?php

namespace Concrete\Package\BaclucEventPackage;

defined('C5_EXECUTE') or exit(_('Access Denied.'));

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Package\Package;
use Doctrine\ORM\EntityManagerInterface;

class Controller extends Package {
    public const PACKAGE_HANDLE = 'bacluc_event_package';
    protected $pkgHandle = self::PACKAGE_HANDLE;
    protected $appVersionRequired = '9.0.1';
    protected $pkgVersion = '0.0.1';
    protected $pkgAutoloaderRegistries = [
        'src' => 'BaclucEventPackage',
    ];

    public function getPackageName() {
        return t('BaclucEventPackage');
    }

    public function getPackageDescription() {
        return t('Package to Manage Events');
    }

    public function install() {
        $pkg = parent::install();
        // add blocktypeset
        if (!Set::getByHandle('bacluc_event_set')) {
            Set::add('bacluc_event_set', 'Appointment', $pkg);
        }
        BlockType::installBlockType('bacluc_event_block', $pkg);
        Set::getByHandle('bacluc_event_set')->addBlockType(BlockType::getByHandle('bacluc_event_block'));
        BlockType::installBlockType('bacluc_next_event_block', $pkg);
        Set::getByHandle('bacluc_event_set')->addBlockType(BlockType::getByHandle('bacluc_next_event_block'));
    }

    public function uninstall() {
        $eventblock = BlockType::getByHandle('bacluc_event_block');
        $nextEventBlock = BlockType::getByHandle('bacluc_next_event_block');
        $em = $this->app->make(EntityManagerInterface::class);
        $em->getConnection()->beginTransaction();

        try {
            if (is_object($eventblock)) {
                $eventblock->delete();
            }
            if (is_object($nextEventBlock)) {
                $nextEventBlock->delete();
            }
            parent::uninstall();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();

            throw $e;
        }
    }

    public function getPackageDependencies() {
        return [
            'bacluc_c5_crud' => true,
        ];
    }
}
