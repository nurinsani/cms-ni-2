@extends('layouts.main')

@section('head-content')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('content')
    @push('css')
        <style>
            @media print {
                .d-print-none {
                    display: none;
                }

                .d-print-flex {
                    display: flex;
                }

                .main-footer {
                    display: none !important;
                }

                @page {
                    margin-left: 0.5in;
                    margin-right: 0.5in;
                    margin-top: 0;
                    margin-bottom: 0;
                }
            }
        </style>
    @endpush

    <div class="row row-cols-1">
        <div class="col">
            <div class="card">
                <div class="card-header mt-5 d-print-flex justify-content-between d-none">
                    <img src="https://picsum.photos/200" width="150px" alt="">
                    <div class="text-center">
                        <h4>LAPORAN PERTANGGUNGJAWABAN PEMBELIAN</h4>
                        <h4>WILAYAH JATUT</h4>
                        <h4>PERIODE AGUSTUS 2023</h4>
                        <h4>TANGGAL 25-8-2023</h4>
                    </div>
                    <div class=""></div>
                </div>
                <div class="card-body">
                    <a href="{{ url('pengurus/approval-lpj') }}" class="btn btn-sm btn-primary mb-3">
                        <i class="fas fa-arrow-left"></i> Kembali</a>
                    <ul class="p-0" style="list-style: none">
                        <li>Nomor : {{ $lpj->nomor }}</li>
                        <li>Kepada : Ketua dan Pengurus KSPPS Nur Insani</li>
                        <li>Dari : {{ $lpj->user->name }}</li>
                        <li>Tanggal : {{ $lpj->tanggal_pengajuan }}</li>
                        <li>Cabang : {{ $lpj->ap->unit ?? $lpj->area->area}}</li>
                    </ul>

                    <div class="table-responsive">
                        {{-- @foreach ($lpj->pengajuan_details as $item)
                        @endforeach
                        @if ($item->kode_area == TRUE)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">COA</th>
                                    <th scope="col">Nama jenis Peralatan</th>
                                    <th scope="col">Merk/Type</th>
                                    <th scope="col">Pengajuan</th>
                                        @foreach ($lpj->pengajuan_details as $item)
                                        @endforeach
                                        @php
                                            $branch = App\Models\Branch::where('code_area', $item->kode_area)->get();
                                        @endphp
                                        @foreach ($branch as $b)
                                        <th>
                                            {{ Str::ucfirst($b->unit) }}
                                        </th>
                                        @endforeach
                                    <th scope="col">Harga Pembelian</th>
                                    <th>Struk pembelian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lpj->pengajuan_details as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        
                                        <td>{{ $item->jenis_peralatan->param_peralatan->coa }}</td>
                                        <td>{{ $item->jenis_peralatan->nama_jenis }}</td>
                                        <td>{{ $item->merk }}</td>
                                        <td>Rp. {{ number_format($item->harga * $item->kuantitas) }}</td>
                                            @foreach ($item->avarage() as $a)
                                            <td class="branch_unit" harga='{{ $a->harga }}' unit='{{ $a->unit }}'>
                                                Rp. {{ number_format($a->harga) }}
                                            </td>
                                            @endforeach
                                        <td>
                                            {{ number_format(($item->harga_pembelian / $branch->count()) * $branch->count()) }}
                                        </td>
                                        <td><a href="" onclick="window.open('{{ url('images/lpj/struk_pembelian/'.$item->struk_pembelian) }}')">{{ $item->struk_pembelian }}</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4"><strong>Jumlah</strong></td>
                                    <td colspan="1"><strong>Rp. {{ number_format($lpj->subtotal()) }}</strong</td>
                                    @foreach ($lpj->pengajuan_details as $item)
                                    @endforeach

                                    @foreach ($item->avarage() as $a)
                                    <td>
                                        <strong id="hasil_{{ $a->unit }}">0</strong>
                                    </td>
                                    @endforeach
                                    <td colspan="1"><strong>Rp. {{ number_format($lpj->total_pembelian()) }}</strong></td>
                                </tr>
                                @if ($lpj->total_pembelian() > $lpj->subtotal())
                                    <tr>
                                        <td colspan="4"><strong>Kekurangan</strong></td>
                                        <td colspan="2"><strong>Rp. {{ number_format($lpj->total_pembelian() - $lpj->subtotal())}}</strong></td> 
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="4"><strong>Sisa/Pengembalian</strong></td>
                                        <td colspan="2"><strong>Rp. {{ number_format($lpj->subtotal() - $lpj->total_pembelian())}}</strong></td> 
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                        @endif --}}
                        
                        {{-- @if ($item->kode_branch == TRUE) --}}
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">AP</th>
                                    <th scope="col">COA</th>
                                    <th scope="col">Nama jenis Peralatan</th>
                                    <th scope="col">Merk/Type</th>
                                    <th scope="col">Pengajuan</th>
                                    <th scope="col">Harga Pembelian</th>
                                    <th>Struk Pembelian</th>
                                    <th>Bukti Pengembalian</th>
                                    {{-- <th scope="col">Aksi</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lpj->pengajuan_details as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->ap->unit ?? $item->area->area}}</td>
                                        <td>{{ $item->jenis_peralatan->param_peralatan->coa }}</td>
                                        <td>{{ $item->jenis_peralatan->nama_jenis }}</td>
                                        <td>{{ $item->merk }} {{ $item->type }}</td>
                                        <td>Rp. {{ number_format($item->harga * $item->kuantitas) }}</td>
                                        <td>Rp. {{ number_format($item->harga_pembelian) }}</td>
                                        <td><a href="" onclick="window.open('{{ url('images/lpj/struk_pembelian/'.$item->struk_pembelian) }}')">{{ $item->struk_pembelian }}</a></td>
                                        <td><a href="" onclick="window.open('{{ url('images/lpj/bukti_pengembalian/'.$lpj->bukti_pengembalian) }}')">{{ $lpj->bukti_pengembalian }}</a></td>

                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6"><strong>Total Uang Muka</strong></td>
                                    <td colspan="3"><strong>Rp. {{ number_format($lpj->subtotal()) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="7"><strong>Total Pembelian</strong></td>
                                    <td colspan="2"><strong>Rp. {{ number_format($lpj->total_pembelian()) }}</strong></td> 
                                </tr>
                                @if ($lpj->total_pembelian() > $lpj->subtotal())
                                    <tr>
                                        <td colspan="7"><strong>Kekurangan</strong></td>
                                        <td colspan="2"><strong>Rp. {{ number_format($lpj->total_pembelian() - $lpj->subtotal())}}</strong></td> 
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="7"><strong>Sisa/Pengembalian</strong></td>
                                        <td colspan="2"><strong>Rp. {{ number_format($lpj->subtotal() - $lpj->total_pembelian())}}</strong></td> 
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                        {{-- @endif --}}
                        
                    </div>

                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function buat_lpj(pengajuan_detail_id) {
            let modal = $('#modal_lpj').modal('show')
            modal.find('#pengajuan_detail_id').val(pengajuan_detail_id)
        }

        var branch_unit = $('.branch_unit')
        var harga_unit = []
        $.each(branch_unit, function(index, value){
            var kode_unit = $(value).attr('unit')
            var harga = $(value).attr('harga')
            
            harga_unit['hasil_' + kode_unit] = harga
            console.log(harga);
        })

        console.log(harga_unit);
    </script>
@endpush
