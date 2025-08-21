<?php

function perch_shop_create_package($data)
{
    $API      = new PerchAPI(1.0, 'perch_shop');
    $Packages = new PerchShop_Packages($API);
    return $Packages->create($data);
}

function perch_shop_add_package_item($packageID, $data)
{
    $API   = new PerchAPI(1.0, 'perch_shop');
    $Items = new PerchShop_PackageItems($API);
    $data['packageID'] = $packageID;
    return $Items->create($data);
}

