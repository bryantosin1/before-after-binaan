<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class legalitasController extends BaseController
{
    public function index()
    {
        $legalitasModel = new \App\Models\LegalitasModel();
        $legalitas = $legalitasModel->findAll();

        return view('legalitas/index', [
            'legalitas' => $legalitas,
        ]);
    }

    public function create()
    {
        return view('legalitas/add_legalitas');
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'legalitas' => 'required',
            'file_legalitas' => 'uploaded[file_legalitas]|max_size[file_legalitas,4096]|ext_in[file_legalitas,pdf]',
        ], [
            'legalitas' => [
                'required' => 'Jenis legalitas harus diisi.'
            ],
            'file_legalitas' => [
                'uploaded' => 'File legalitas harus diupload.',
                'max_size' => 'Ukuran file legalitas maksimal 4MB.',
                'ext_in' => 'File legalitas harus berformat PDF.'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // dd(session()->get('user_id'));

        $fileLegalitas = $this->request->getFile('file_legalitas');

        $namaFile = $fileLegalitas->getRandomName();

        $fileLegalitas->move('storage', $namaFile);
        // dd($namaFile);

        $legalitasModel = new \App\Models\LegalitasModel();
        $legalitasModel->insert([
            // 'user_id_legalitas' => 2,
            'user_id_legalitas' => session()->get('user_id'),
            'legalitas' => $this->request->getPost('legalitas'),
            'file_legalitas' => $namaFile,
            'tipe' => '1',
        ]);

        return redirect()->to('user/legalitas')->with('success', 'legalitas berhasil ditambahkan');
    }

    public function edit($id_legalitas)
    {
        $legalitasModel = new \App\Models\LegalitasModel();
        $legalitas = $legalitasModel->find($id_legalitas);

        if (!$legalitas) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("legalitas dengan ID $id_legalitas tidak ditemukan.");
        }

        return view('legalitas/edit_legalitas', [
            'legalitas' => $legalitas,
        ]);
    }

    public function update($id_legalitas)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama_legalitas' => 'required',
            'tanggal_legalitas' => 'required',
            'lokasi_legalitas' => 'required',
            'deskripsi_legalitas' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $legalitasModel = new \App\Models\LegalitasModel();
        $legalitasModel->update($id_legalitas, [
            'nama_legalitas' => $this->request->getPost('nama_legalitas'),
            'tanggal_legalitas' => $this->request->getPost('tanggal_legalitas'),
            'lokasi_legalitas' => $this->request->getPost('lokasi_legalitas'),
            'deskripsi_legalitas' => $this->request->getPost('deskripsi_legalitas'),
        ]);

        return redirect()->to('/legalitas')->with('success', 'legalitas berhasil diperbarui');
    }
}