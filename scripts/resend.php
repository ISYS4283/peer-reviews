<?php require_once __DIR__.'/../vendor/autoload.php';

if ( empty($argv[1]) ) {
    throw new Exception('filename required as $argv[1]');
}

$file = $argv[1];
if ( !is_readable($file) ) {
    throw new Exception("$file is not readable");
}

$distribution = file_get_contents($file);
if ( false === $distribution ) {
    throw new Exception("file_get_contents error with $file");
}

$distribution = json_decode($distribution, true);
if ( !is_array($distribution) ) {
    throw new Exception("json_decode error: ".json_last_error_msg());
}

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();
$dotenv->required([
    'MAIL_HOST',
    'MAIL_PORT',
    'MAIL_USERNAME',
    'MAIL_PASSWORD',
    'MAIL_ENCRYPTION',
])->notEmpty();

$mailer = Swift_Mailer::newInstance(
    Swift_SmtpTransport::newInstance(
        getenv('MAIL_HOST'),
        getenv('MAIL_PORT'),
        getenv('MAIL_ENCRYPTION')
    )
    ->setUsername(getenv('MAIL_USERNAME'))
    ->setPassword(getenv('MAIL_PASSWORD'))
);

$message = "Hello, please review the following blogs:\n\n";

$message .= "Refer back to this link for directions: https://github.com/ISYS4283/peer-reviews\n\n";

foreach ( $distribution as $username => $reviews ) {
    $mailer->send(Swift_Message::newInstance('ISYS 4283 Blog Review')
        ->setFrom("isys4283@uark.edu")
        ->setTo("$username@uark.edu")
        ->setCc("jpucket@uark.edu")
        ->setBody($message . print_r($reviews, true))
    );
    sleep(1);
}
