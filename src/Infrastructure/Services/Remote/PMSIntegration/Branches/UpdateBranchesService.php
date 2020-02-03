<?php

namespace App\Infrastructure\Services\Remote\PMSIntegration\Branches;

use App\Infrastructure\Entity\ServiceapiBranch;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of UpdateBranchesService
 *
 * @author i.goroshyn
 */
class UpdateBranchesService {

    /**
     * BRANCH_TYPE.
     */
    private const BRANCH_TYPE = 1;//Отделение
    /**
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * saveToLocalStorage.
     */
    public function saveToLocalStorage(
        \stdClass $branchesCollection,
        \stdClass $attikaBranches
    ) {
        if(true != $branchesCollection->response->status){
            throw new \Exception('No data was fetched!');
        }

        $this->clearBranchesTable();

        foreach($branchesCollection->data as $branchNum => $branch)
        {
            $departmentId = intval($branch->fields->departNumber);

            if(property_exists($attikaBranches->result, $departmentId)){
                $attikaBranch = $attikaBranches->result->{$departmentId};
            } else {
                $attikaBranches->result->photos = [];
                $attikaBranches->result->public = [
                    "public_description_ru" => "",
                    "public_description_ua" => "",
                    "public_description_en" => "",
                    "navigation_ru" => "",
                    "navigation_ua" => "",
                    "navigation_en" => ""
                ];
                $attikaBranches->result->services = [ 
                    "monobank" => 0,
                    "cardpay" => 0,
                    "vending" => 0,
                    "remittance" => 0,
                    "fitting" => 0,
                    "3mob" => 0,
                    "uplata" => 0,
                    "joint" => 0
                ];
                $attikaBranches->result->schedule = "ПН-НД 08:00:00-20:00:00";
            }

            $this->updateBranch($branch->fields, $attikaBranch);
        }
    }

    /**
     * Function createBranch.
     */
    private function updateBranch(\stdClass $PMSbranch, \stdClass $attikaBranch)
    {
        if(!$this->hasBranch($PMSbranch->branch))
        {

            if (isset($PMSbranch->departNumber) 
                || !empty($PMSbranch->departNumber)
            ){
                $branch = new ServiceapiBranch();
                $branch
                    ->setAdress($PMSbranch->address)
                    ->setDeliveryBranchId(intval($PMSbranch->branch))
                    ->setDescription($PMSbranch->Depart->descr)
                    ->setFormat($PMSbranch->branchType->uuid)
                    ->setLat(floatval($PMSbranch->lat))
                    ->setLng(floatval($PMSbranch->lng))
                    ->setLocality($PMSbranch->city->descr)
                    ->setMaxWeight($PMSbranch->weight_limit)
                    ->setNumber(intval($PMSbranch->departNumber))
                    ->setPhotos(json_encode($attikaBranch->photos))
                    ->setPublic(json_encode($attikaBranch->public))
                    ->setServices(json_encode($attikaBranch->services))
                    ->setSheduleDescription($attikaBranch->schedule)
                    ->setType(self::BRANCH_TYPE)
                    ->setUpdatetime(\strtotime("now"));
            }

            $this->entityManager->persist($branch);
            $this->entityManager->flush();   
        }
    }

    /**
     * 
     * @param ServiceapiBranch $branch
     * @return bool
     */
    private function hasBranch($deliveryBranchId): bool
    {
        return (bool)$this
            ->entityManager
            ->getRepository(ServiceapiBranch::class)
            ->hasBranch($deliveryBranchId);
    }
    
    private function clearBranchesTable(): bool
    {
        return (bool)$this
            ->entityManager
            ->getRepository(ServiceapiBranch::class)
            ->clearAll();
    }
}
