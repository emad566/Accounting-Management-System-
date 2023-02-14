@extends('dashboard.master', ['form'=>1])

@section('content')
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">tables Styles</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('states.index') }}" class="btn btn-primary float-right">كل المحافظات</a>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- /End Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6>tables Styles</h6>
                    <hr>
                    <div class="responsive-container">
                        <table class="display nowrap table table-hover table-striped table-bordered datatable dataTable no-footer" cellspacing="0" width="100%" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info" style="width: 100%;">

                            <tr role="row"><th class="sorting_asc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="#id: تفعيل لترتيب العمود تنازلياً" style="width: 49px;"><input type="checkbox" name="allItems" value="1" id="allItems" class="allItems">#id</th><th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="الإجراءات: تفعيل لترتيب العمود تصاعدياً" style="width: 100px;">الإجراءات</th><td class="id sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="المعرف: تفعيل لترتيب العمود تصاعدياً" style="width: 74px;">المعرف</td><td class="r_name sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="المحافظة: تفعيل لترتيب العمود تصاعدياً" style="width: 109px;">المحافظة</td><th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="الإجراءات: تفعيل لترتيب العمود تصاعدياً" style="width: 102px;">الإجراءات</th></tr>


                        <tr class="rowAction odd" role="row">
                                <td class="sorting_1" data-th="sdsc"><input type="checkbox" name="states[]" value="1" class="boxItem">1</td><td class="actionLinks"><a href="https://marvel-inter.com/dashboard/states/1/edit"><i class="fas fa-edit delEdit"></i></a>
                            <a href="https://marvel-inter.com/dashboard/states/1/delete" msg=" :المحافظة  الإسكندرية" class="deleteMe"><i class="fas fa-trash-alt delEdit"></i></a>

                                </td><td class="id">1</td><td class="r_name">الإسكندرية</td><td class="actionLinks"><a href="https://marvel-inter.com/dashboard/states/1/edit"><i class="fas fa-edit delEdit"></i></a>
                            <a href="https://marvel-inter.com/dashboard/states/1/delete" msg=" :المحافظة  الإسكندرية" class="deleteMe"><i class="fas fa-trash-alt delEdit"></i></a>

                                </td></tr><tr class="rowAction even" role="row">
                                <td class="sorting_1"><input type="checkbox" name="states[]" value="2" class="boxItem">2</td><td class="actionLinks"><a href="https://marvel-inter.com/dashboard/states/2/edit"><i class="fas fa-edit delEdit"></i></a>
                            <a href="https://marvel-inter.com/dashboard/states/2/delete" msg=" :المحافظة  الإسماعيلية" class="deleteMe"><i class="fas fa-trash-alt delEdit"></i></a>

                                </td><td class="id">2</td><td class="r_name">الإسماعيلية</td><td class="actionLinks"><a href="https://marvel-inter.com/dashboard/states/2/edit"><i class="fas fa-edit delEdit"></i></a>
                            <a href="https://marvel-inter.com/dashboard/states/2/delete" msg=" :المحافظة  الإسماعيلية" class="deleteMe"><i class="fas fa-trash-alt delEdit"></i></a>

                                </td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .responsive-container{
        width: 100%;
        margin: 0 auto;
        padding-top:15p;
        margin-top:80px;
        border-radius: 5px;
        box-shadow: 0px 0px 6px#666;
        background: white;

    }

    .responsive-container table{
        width: 100%;
    }

    /* .responsive-container td{
        border: solid 1px #ccc;
    } */

    .responsive-container td::before{
        display: none;
    }

    .responsive-container th, .responsive-container td{
        display: table-cell;
        padding: 10px;
        text-align: right;
    }

    @media(max-width: 767px){
        .responsive-container{
            width: 100%;
        }
        .responsive-container tr:first-child{
            display: none;
        }
        .responsive-container .actionLinks{
            display: none;
        }
        .responsive-container tr{
           border: solid 1px #666;
           margin-top: 5px;
        }

        .responsive-container td{
            display: block;
            border: none;
        }

        .responsive-container td:first-child{
            padding-top:0.5em;
        }
        .responsive-container td::before{
            content: attr(data-th) ": ";
            font-weight: bold;
            width: 6.5em;
            display: inline-block;
        }
    }
</style>

<?php execution_time($start_time); ?>
@endsection

@section('script')

@endsection

