<?php
/**
 * Created by PhpStorm.
 * User: yvanwatchman
 * Date: 25-04-18
 * Time: 11:44
 */
require('stiply.php');

use Cyberfusion\Stiply;

$user = array();
$user['username'] = 'ywatchman@cyberfusion.nl';
$user['password'] = 'SuperSecretFromOurBase';
$stiply = new Stiply($user);

$data = array(
    'file' => new CURLFile("contract.pdf"),
    'term' => '1w',
    'message' => 'Please sign the contract in order to start using our services.'
);

// Additional fields available such as read-only
$signers = [
    0 => [
        'signer_email' => strip_tags(trim("ywatchman@cyberfusion.nl")),
        'signer_signature_fields' => [['name' => 'signature_0']],
        'signer_text_fields' => [['name' => 'text_0'], ['name' => 'datum_0']]
    ],
    1 => [
        'signer_email' => strip_tags(trim("customer@client.nl")),
        'signer_signature_fields' => [['name' => 'signature_1']],
        'signer_text_fields' => [['name' => 'text_1'], ['name' => 'datum_1']]
    ],
];

$stiply->createSignRequest($data, "post", "Contract", $signers);
