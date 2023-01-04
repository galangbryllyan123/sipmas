<?php

/**
 * BISMILLAHIRROHMANIRROHIM
 * Author   : ajakyuk.skom.id
 * Nama App : sipmas (Sistem Pengaduan Masyarakat)
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {
    private $_table = "users";

    public function getAll()
    {
        return $this->db->get($this->_table)->result_array(); //ambil semua data
    }

    function getById($id)
    {
        return $this->db->get_where($this->_table, ["id" => $id])->row();
    }

    function Login()
    {
        //membuat variabel post
        $post = $this->input->post();


        //ambil email atau username yang sesuai dengan post
        //agar user bisa login menggunakan kedua itu
        $this->db->where('email', $post['email'])
                ->or_where('username', $post['email']);
        $user = $this->db->get($this->_table)->row_array();
        //masukan kedalam variabel user

        if ($user)
        { //user ada
            if (password_verify($post['password'], $user['password']))
            {
                //cek password dan simpan session
                $data = array(
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                );
                $this->session->set_userdata($data);

                //cek role
                if($user['role'] == "admin") {
                   redirect('administrator');
                } elseif ($user['role'] == "petugas") {
                   redirect('petugas');
                } else {
                    redirect('masyarakat');
                }
                
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Kata sandi salah!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email atau Username belum terdaftar!</div>');
            redirect('auth');
        }

    }

  public function dataUsers() //data users sesuai username yang ada disession
  {
      return $this->db->get_where('users', ['username' => $this->session->userdata('username')])->row_array();
  }

  public function dataAdmin() //data untuk admin sesuai session
  {
      return $this->db->get_where('admin', ['username' => $this->session->userdata('username')])->row_array();
  }

  public function adminAll()
  {
      return $this->db->get('admin')->result_array();
  }

    public function dataMasyarakatRow() //masyarakat satu baris sesuai session
    {
        return $this->db->get_where('warga', ['username' => $this->session->userdata('username')])->row_array();
    }

    public function dataMasyarakatResult() //KHUSUS ADMIN
    {
        return $this->db->get('masyarakat')->result_array();
    }

    public function dataPetugasRow() //petugas satu baris sesuai session
    {
        return $this->db->get_where('petugas', ['username' => $this->session->userdata('username')])->row_array();
    }

    public function dataPetugasResult() //KHUSUS ADMIN
    {
       return $this->db->get('petugas')->result_array();
    }

    public function joinKategoriPetugas()
    {
        $this->db->select('*');
        $this->db->from('petugas');
        $this->db->join('kategori', 'kategori.id_kategori = petugas.id_kategori');
        return $this->db->get()->result_array();
    }

    public function get_masyarakat($nik)
    {
        $this->db->select('*');
        $this->db->from('warga');
        $this->db->where('nik', $nik);

        return $this->db->get()->row_array();
    }
public function register(){
        $email = addslashes(htmlspecialchars($this->input->post('email', true)));
        $checkEmail = $this->db->get_where('user', ['email' => $email])->row_array();
        if($checkEmail){
            $this->session->set_flashdata('failed', '<div class="alert alert-danger" role="alert">
            Email sudah ada!
            </div>');
            redirect(base_url() . 'register');
        }else{
            $name = addslashes(htmlspecialchars($this->input->post('name', true)));
            $password = $this->input->post('password');
            $token = sha1(rand());
            function textToSlug($text='') {
                $text = trim($text);
                if (empty($text)) return '';
                $text = preg_replace("/[^a-zA-Z0-9\-\s]+/", "", $text);
                $text = strtolower(trim($text));
                $text = str_replace(' ', '-', $text);
                $text = $text_ori = preg_replace('/\-{2,}/', '-', $text);
                return $text;
            }
            $username = textToSlug($name);
            $checkUsername = $this->db->get_where('user', ['username' => $username])->row_array();
            if($checkUsername){
                $username = $username . substr(rand(),0,3);
            }
            $data = [
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'date_register' => date('Y-m-d H:i:s'),
                'token' => $token,
                'photo_profile' => 'default.png'
            ];
            $this->db->insert('user', $data);

            $data = [
                'email' => $email,
                'date_subs' => date('Y-m-d H:i:s'),
                'code' => time() . rand()
            ];
            $this->db->insert('subscriber', $data);

            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['useragent'] = $this->Settings_model->general()["app_name"];
            $config['smtp_crypto'] = $this->Settings_model->general()["crypto_smtp"];
            $config['protocol'] = 'smtp';
            $config['mailtype'] = 'html';
            $config['smtp_host'] = $this->Settings_model->general()["host_mail"];
            $config['smtp_port'] = $this->Settings_model->general()["port_mail"];
            $config['smtp_timeout'] = '5';
            $config['smtp_user'] = $this->Settings_model->general()["account_gmail"];
            $config['smtp_pass'] = $this->Settings_model->general()["pass_gmail"];
            $config['crlf'] = "\r\n";
            $config['newline'] = "\r\n";
            $config['wordwrap'] = TRUE;

            $this->email->initialize($config);
            $this->email->from($this->Settings_model->general()["account_gmail"], $this->Settings_model->general()["app_name"]);
            $this->email->to($email);
            $this->email->subject('Verifikasi Alamat Email '.$this->Settings_model->general()["app_name"]);
            $this->email->message(
                '<p><strong>Halo '.$name.'</strong><br>
                Terima kasih telah mendaftar di '.$this->Settings_model->general()["app_name"].'. <br/>
                Silakan verifikasi email dengan klik link dibawah ini: <br/>
                <a href="'.base_url().'auth/verification?email='.$email.'&token='.$token.'">'.base_url().'auth/verification?email='.$email.'&token='.$token.'</a><br/>
                Terima kasih</p>
                ');
            $this->email->send();
        }
    }
	
}
