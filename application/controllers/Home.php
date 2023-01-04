<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
  function index()
  {
      $data['title'] = "Home - SIPMAS";
      $this->load->view('home', $data);
  }
}
