<?php


namespace App\Validation;


use GuzzleHttp\Psr7\Response;
use Slim\Flash\Messages;

class ValidateForm
{

    private array $data;
    private string $url;

    public function __construct(array $data, string $url)
    {
        $this->data = $data;
        $this->url = $url;
    }

    /**
     * Validate form's data. if valid send mail, else return errors
     * @return ValidationError[]|Response|string[]
     */
    public function validate()
    {

        $validator = (new Validator($this->data))
            ->required('name', 'email', 'message')
            ->textLength('name', 5)
            ->email('email')
            ->mustBeEmpty('info')
            ->textLength('message', 10);

        if ($validator->isValid()) {
            $to      = getEnvValue('MAIL_ADMIN_ADDRESS');
            $subject = 'Formulaire de contact de votre site';
            $message = $this->data['message'];
            $headers = [
                'From' => $this->data['name'],
                'Reply-To' => $this->data['email'],
                'X-Mailer' => 'PHP/' . phpversion()
            ];
            mail($to, $subject, $message, $headers);
            $succes = $validator->sendSuccess('Merci pour votre email');
            $flash = new Messages();
            $flash->addMessage('success', $succes['success']);
            return new Response(302, ['Location' => $this->url]);
        } else {
            return $validator->getErrors();
        }
    }

    public function getValues()
    {
        return $this->data;
    }
}