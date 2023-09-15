<?php

namespace App\Services\MetaTrader5\User;

use AleeDhillon\MetaFive\Entities\User;
use AleeDhillon\MetaFive\Facades\Client;
use App\Models\User as UserModel;
use App\Repositories\UsersRepo;
use App\Services\Auth\CreateTradingAccount;
use App\Services\Auth\CreateTradingUser;
use App\Services\MetaTrader5\BaseApi;
use App\Services\MetaTrader5\Trade\FetchTradingAccounts;
use App\Services\MetaTrader5\User\FetchUser;

class CreateUser extends BaseApi
{
    public function execute(UserModel $user, $mainPassword, $investorPassword, $group, $leverage, $accountType, $leadCampaign = null, $leadSource = null, $remarks = null): User
    {
        $metaUser = new User();
        $metaUser->setName($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name);
        $metaUser->setEmail($user->email);
        $metaUser->setGroup($group);
        $metaUser->setLeverage($leverage);
        $metaUser->setPhone($user->phone);
        $metaUser->setAddress($user->address_1 . ' ' . $user->address_2);
        // $metaUser->setCity("");
        // $metaUser->setState("");
        $metaUser->setCountry($user->country);
        $metaUser->setZipCode($user->postcode);
        /* $metaUser->setMainPassword("NqK9rqY7Z3LK73cZ");
        $metaUser->setInvestorPassword("2aBJh0aWzEJ69BpZ");
        $metaUser->setPhonePassword("XZ55iIL7Qo0ryPaX"); */
        $metaUser->setMainPassword($mainPassword);
        $metaUser->setInvestorPassword($investorPassword);
        $metaUser->setPhonePassword($user->password);
        $metaUser->setLeadCampaign($leadCampaign);
        $metaUser->setLeadSource($leadSource);
        $metaUser->setComment($remarks);
        $metaUser = Client::createUser($metaUser);

        (new CreateTradingUser)->execute($user, (new FetchUser)->execute($metaUser->getLogin()), $accountType, $remarks);
        (new CreateTradingAccount)->execute($user, (new FetchTradingAccounts)->execute($metaUser->getLogin()), $accountType);

        return $metaUser;
    }
}
