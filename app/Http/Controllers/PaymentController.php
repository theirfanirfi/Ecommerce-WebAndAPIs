<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\PaymentExecution;
use URL;
class PaymentController extends Controller
{
    //
    private $_api_context;
    public function __construct(){
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );

        $this->_api_context->setConfig($paypal_conf['settings']);
    }

public function pay(){
    $payer = new Payer();
        $payer->setPaymentMethod('paypal');


        //here items should be defined.
        $item_1 = new Item();
        $item_1->setName('Item 1') /** item name **/
            ->setCurrency('USD')
            ->setQuantity(1)
           // ->setPrice($request->get('amount')); /** unit price **/
            ->setPrice(200); /** unit price **/
        $item_list = new ItemList();


//items should be added to the items list.
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal(200);

            //transaction for the item list should be performed.
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');

            //redirect urls
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::to('paid')) /** Specify return URL **/
            ->setCancelUrl(URL::to('paid'));

            //start the payment intent
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/


        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {
                session()->put('error', 'Connection timeout');
                return redirect('/');
            } else {
                session()->put('error', 'Some error occur, sorry for inconvenience.');
                return redirect('/');
            }
        }
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }
        /** add payment ID to session **/
        session()->put('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return redirect($redirect_url);
        }
        session()->put('error', 'Unknown error occurred');
        return redirect('/');
    }

    public function getPaymentStatus(Request $req)
    {
        /** Get the payment ID before session clear **/
        $payment_id = session()->get('paypal_payment_id');
        $payment_id = $req->input('paymentId');

        if (empty($req->get('PayerID')) || empty($req->get('token'))) {
            session()->put('error', 'Payment failed');
            return redirect('/');
        }
        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($req->get('PayerID'));
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);
        if ($result->getState() == 'approved') {
            session()->put('success', 'Payment success');
            // clear the session payment ID **/
             session()->forget('paypal_payment_id');
            return redirect('/');
        }
        session()->put('error', 'Payment failed');
        return redirect('/');
    }
}
