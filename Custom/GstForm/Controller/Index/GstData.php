<?php
namespace Custom\GstForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Session;

class GstData extends Action
{
    protected $jsonFactory;
    protected $checkoutSession;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->_checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $response = $this->jsonFactory->create();
        $postData = $this->getRequest()->getPostValue();
        $gstNumber = $postData["gst_number"];

        // API endpoint URL
        $encryption_apiUrl = 'https://www.truthscreen.com/InstantSearch/encrypted_string';
        $request_apiUrl = 'https://www.truthscreen.com/api/v2.2/idsearch';
        $decryption_apiUrl = 'https://www.truthscreen.com/InstantSearch/decrypt_encrypted_string';


        // Data to be sent in the POST request (as a JSON string)
        $data = '{
            "transID": "32323",
            "docType": 23,
            "docNumber": "'.$gstNumber.'"
        }';
        // dd($data);
        // Headers
        $headers = [
            'Username: prod.havells@authbridge.com',
            'Content-Type: application/json',
        ];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $encryption_apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1); // Set to POST request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Set POST data as JSON string
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set headers
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string

        // Execute cURL session and store the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            // echo 'cURL Error: ' . curl_error($ch);
        } else {
            // Process the API response

           $requestData = '{"requestData":" '.$response.' "}';
            // echo 'API Response: ' . $requestData ;
        }

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $request_apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1); // Set to POST request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData); // Set POST data as JSON string
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set headers
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string

        // Execute cURL session and store the response
        $request_response = curl_exec($ch);


        curl_setopt($ch, CURLOPT_URL, $decryption_apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1); // Set to POST request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_response); // Set POST data as JSON string
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set headers
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string

        // Execute cURL session and store the response
        $gstDataCollection = curl_exec($ch);
        // dd($gstDataCollection);
        $this->_checkoutSession->setVarValue($gstDataCollection);
        // Close cURL session
        curl_close($ch);

        // dd($this->_checkoutSession->getVarValue());
        // dd($postData);

    $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
    $resultJson->setData($this->_checkoutSession->getVarValue());
    return $resultJson;
        // $result = ['success' => true, 'message' => 'Data saved successfully.']; // Adjust as needed
        // return $response->setData($result);
    }
}
