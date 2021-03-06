<?php require_once __DIR__.'/../vendor/autoload.php';

$endpoint = 'https://blog.isys4283.walton.uark.edu/wp-json/semantic-scale/v1/leaderboard';

$client = new GuzzleHttp\Client();

$response = $client->request('GET', $endpoint);

if ( $response->getStatusCode() === 200 ) {
    $leaderboard = json_decode( $response->getBody(), true );

    foreach ( $leaderboard as $blog ) {
        $blogs[$blog['name']] = ceil( ($blog['score'] / 100) * 5 );
    }

    ksort($blogs);
    print_r($blogs);
}
