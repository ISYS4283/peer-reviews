<?php require_once __DIR__.'/../vendor/autoload.php';

$endpoint = 'https://blog.isys4283.walton.uark.edu/wp-json/semantic-scale/v1/leaderboard';

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();
$dotenv->required([
    'MAIL_HOST',
    'MAIL_PORT',
    'MAIL_USERNAME',
    'MAIL_PASSWORD',
    'MAIL_ENCRYPTION',
])->notEmpty();

$client = new GuzzleHttp\Client();

$response = $client->request('GET', $endpoint);

if ( $response->getStatusCode() === 200 ) {
    $leaderboard = json_decode( $response->getBody(), true );

    foreach ( $leaderboard as $blog ) {
        $blogs[$blog['name']] = $blog['guid'];
    }

    $distribution = jpuck\PeerValueDistributer::distribute($blogs, $count = 3);

    $timestamp  = date("c");
    $production = getenv('APP_ENV');
    $filename   = "peer-review-assignments-$production-$timestamp.json";
    file_put_contents($filename, json_encode($distribution, JSON_PRETTY_PRINT));

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
        sleep(2);
    }
}
