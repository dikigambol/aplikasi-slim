<?php

use Slim\App;

return function (App $app) {
    // e.g: $app->add(new \Slim\Csrf\Guard);

    $app->add(function ($request, $response, $next) {

        $key = $request->getQueryParam("key");

        if (!isset($key)) {
            return $response->withJson(["status" => "Request API key.!"], 401);
        }

        $sql = "SELECT * FROM api WHERE ReqApi_key='".$key."' ";
        $smt = $this->db->prepare($sql);
        $smt->execute([":ReqApi_key" => $key]);

        if ($smt->rowCount() > 0){
            $result = $smt->fetch();
            if ($key == $result["ReqApi_key"]) {

                $sql = "UPDATE api SET ReqApi_hit=ReqApi_hit+1 WHERE ReqApi_key=:ReqApi_key";
            }
        }

        return $response = $next($request, $response);

    }); 

        

    

};
