<?php

namespace App\Controllers;


use App\Models\Image;
use App\Models\User;
use Cocur\Slugify\Slugify;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\UploadedFile;
use Respect\Validation\Validator as v;

class BaseController
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function render(ResponseInterface $response, $file, $params = [])
    {
        return $this->container->view->render($response, $file, $params);
    }

    public function redirect($response, $name, $status, $params = array())
    {
        $url = $this->router->pathFor($name,$params);
        return $response->withStatus($status)->withHeader('Location', $url);
    }

    public function validate(Request $request, $rules  = [])
    {
        return $this->container->validator->validate($request, $rules);
    }

    public  function setFlash($message, $type = 'success')
    {
        if(!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }

        return $_SESSION['flash'][$type] = $message;
    }

    public function setUser($user)
    {
        if($user instanceof User){
            $_SESSION['user']['id'] = $user->id;
            $_SESSION['user']['name'] = $user->name;
        }
    }

    public function slugify(Request $request)
    {
        if(!empty($request->getParam('slug'))){
            $validation = $this->validate($request, ['slug' => v::slug()]);

            if(!$validation->failed()){
                return $request->getParam('slug');
            }
        }

        $slug = new Slugify();
        return $slug->slugify($request->getParam('title'));
    }

    public function deleteImg(Image $img)
    {
        $imgName = $img->name;
        $img->delete();
        unlink($this->upload_directory . DIRECTORY_SEPARATOR . $imgName);
    }

    public static function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public function __get($name) {
        return $this->container->$name;
    }

    public function dump($variable)
    {
        echo '<pre>';
        print_r($variable);
        echo '</pre>';
    }

    public static function sendMail(Request $request, $mailer, $subject, $messageBody)
    {
        $fullName = $request->getParam('name').' '.$request->getParam('surname');

        $message = \Swift_Message::newInstance($subject)
            ->setFrom([$request->getParam('email')  => $fullName])
            ->setTo('cosmin.dumitrascu@gmail.com')
            ->setBody($messageBody);

        $sent = $mailer->send($message);

        return $sent;
    }
}