<?php

namespace App\Infrastructure\Services\Remote\AttikaIntegration\Branches;

use App\Infrastructure\Services\Remote\AttikaIntegration\AttikaFetcher;

/**
 * Description of FetchBranchesService
 *
 * @author i.goroshyn
 */
class   FetchBranchesService {
    
    /**
     *
     * @var string 
     */
    private $login = 'serviceapi';

    /**
     *
     * @var string 
     */
    private $key = '5b303311d7eebcd1ee6fc98f205784a1bae45a509ed887e56294176a6116331ec706a2f8c26f7171bd0803e932697126a3f5fea795b16b6de6b29fda27b420a53e15c3bb9b5e45e1fa5076f2fc34a365fc12a60844c72f73274328b660a05a15d87c3356841573781f0407537c11d8e79bfcd916e0303cf516e06ca244a78c691cc8bc853306b4b4d1f6b70523c20e01ff6842cb0d5a45bb532164ecf8cb7be73804c58a538ef783456f24c76d839db6a9a13f60e06112077cd05b24c4aea197d7ac1ba928166c8ed6bd69cc7535eab4beb29b0b8fb43973f2a5fa80cf4c987700161d85ba57e55bfd3c5da754fcabf6f0e41e5a4e078c380c8098585389347b';

    /**
     *
     * @var AttikaFetcher
     */
    private $attikaFetcher;

    /**
     * 
     */
    public function __construct(AttikaFetcher $attikaFetcher)
    {
        $this->attikaFetcher = $attikaFetcher;
    }

    /**
     * 
     * @param type $PMSBranches
     * @return type
     */
    public function fetchBranchesFromAttika($PMSBranches)
    {
        $branchNumbers = $this->parseBranchNumbers($PMSBranches);

        $post = array(
            "login" => $this->login,
            "request" => "getData",
            "type" => "request",
            "method" => "branches_info",
            "searchby" => "number",
            "searchdata" => $branchNumbers,
            "output" => "full",
        );

        return $this->attikaFetcher->fetch($this->preparePost($post));
    }

    /**
     * 
     * @param type $postArray
     * @return type
     */
    private function preparePost($postArray)
    {        
        $postArray['datetime'] = date('Y-m-d H:i:s');
        $postArray['sign'] = '';
        $post = json_encode($postArray) . $this->key;
        $sign = sha1($post, true);
        $sign = bin2hex($sign);
        $postArray['sign'] = $sign;

        return json_encode($postArray);
    }
    
    /**
     * 
     */
    private function parseBranchNumbers($PMSBranches): array
    {
        $result = [];
        foreach($PMSBranches->data as $branch){
            $branchNumber = intval($branch->fields->departNumber);
            $result[$branchNumber] = $branchNumber;
        }

        return $result;
    }
}
