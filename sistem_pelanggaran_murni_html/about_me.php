<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors', '1'); error_reporting(E_ALL);
require_once 'auth.php'; 
require_once 'koneksi.php';

// Panggil header terpusat anu sarua jeung menu séjén
include 'header.php';
?>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
                <h1 class="h2"><i class="fas fa-info-circle text-primary mr-2"></i> Tentang Pembuat Aplikasi</h1>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                        <!-- Header Profil dengan Gradasi & Foto -->
                        <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #007bff, #6610f2);">
                            <div class="mx-auto mb-3 rounded-circle shadow border border-white" style="width: 100px; height: 100px; overflow: hidden; background: #fff;">
                                <img src="foto.jpg" alt="Azril Akbar Fawji Abdillah" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/100?text=Foto';">
                            </div>
                            <h3 class="mb-1 font-weight-bold">Azril Akbar Fawji Abdillah</h3>
                            <p class="text-white-50 mb-0"><i class="fas fa-laptop-code mr-1"></i> Siswa Rekayasa Perangkat Lunak (RPL)</p>
                        </div>
                        
                        <!-- Body Detail Informasi -->
                        <div class="card-body p-4">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td style="width: 30%; font-weight: 600;"><i class="fas fa-school text-primary mr-2"></i> Kelas</td>
                                        <td style="width: 5%;">:</td>
                                        <td><span class="badge badge-primary px-3 py-2">XII RPL 1</span></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;"><i class="fas fa-code text-success mr-2"></i> Jurusan</td>
                                        <td>:</td>
                                        <td>Rekayasa Perangkat Lunak</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;"><i class="fas fa-heart text-danger mr-2"></i> Cita-cita</td>
                                        <td>:</td>
                                        <td>Apa adanya</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 600;"><i class="fas fa-project-diagram text-warning mr-2"></i> Project UKK</td>
                                        <td>:</td>
                                        <td><strong>Sistem Informasi Pelanggaran Siswa (UKK 2027)</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

<?php 
// Panggil footer terpusat
include 'footer.php'; 
?>