<?php

function perch_shop_create_package($data)
{
    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
    $Package  = $Packages->create($data);

    if ($Package) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['perch_shop_package_id'] = $Package->id();
    }

    return $Package;
}

function perch_shop_add_package_item($packageID, $data)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
    $Items = new PerchShop_PackageItems($API);
    $data['packageID'] = $packageID;
    return $Items->create($data);
}

function perch_shop_package_update_item($itemID, $qty)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
    $Items = new PerchShop_PackageItems($API);
    $Item  = $Items->find((int)$itemID);
    if ($Item) {
        return $Item->update(['qty' => (int)$qty]);
    }
    return false;
}

function perch_shop_package_remove_item($itemID)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
    $Items = new PerchShop_PackageItems($API);
    $Item  = $Items->find((int)$itemID);
    if ($Item) {
        return $Item->delete();
    }
    return false;
}

function perch_shop_update_package_status($status)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['perch_shop_package_id'])) {
        return false;
    }

    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
    $Package  = $Packages->find((int)$_SESSION['perch_shop_package_id']);

    if ($Package) {
        return $Package->update(['status' => $status]);
    }

    return false;
}

