# StiplyAPI
Easy to use API wrapper for Stiply, create sign requests, add signers.


#### Including Stiply in your project

    <?php
    require_once('stiply.php');
    use Cyberfusion\Stiply;
    
    $user = array();
    $user['username'] = 'ywatchman@cyberfusion.nl';
    $user['password'] = 'SuperSecretFromOurBase';
    $stiply = new Stiply($user);
    
#### Creating a sign request
    
    $data = [
        'file' => new CURLFile("contract.pdf"),
        'term' => '1w',
        'message' => 'Please sign the contract in order to start using our services.'
    ]
    
    $signers = [
        0 => [
            'signer_email' => strip_tags(trim("ywatchman@cyberfusion.nl")),
            'signer_signature_fields' => [['name' => 'signature_0']],
            'signer_text_fields' => [['name' => 'text_0'], ['name' => 'date_0']]
        ],
    ];
    
    $stiply->createSignRequest($data, "post", "Contract", $signers);
    
That's all.

The $signers variable is an array, so you could add more signers and it will iterate over all the signers and adds them to the request.

If something isn't working as it supposed to be, please add an issue and we'll try to get to it asap and feel free to contribute.

#### Todo:

 - [x] Creating a sign request
 - [x] Adding multiple signers
 - [x] Cancelling / Deleting a sign request
 - [x] Sending a reminder
 - [ ] Error handling reminder
 - [ ] Extending a term of a sign request
 - [ ] Updating a signer (Optional)
 
