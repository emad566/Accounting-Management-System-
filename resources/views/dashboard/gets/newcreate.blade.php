@extends('dashboard.master', ['form'=>1])

@section('content')
<style>
    .alertMsg{
        display: flex;
        flex-direction: column;
        row-gap: 20px;
    }
    .bgGreen{
        background-color: #f90
    }
</style>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">تحصيل/مرتجع</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- /End Bread crumb and right sidebar toggle Emad test -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body" style="min-height: 100vh;">
                    @include('dashboard.gets.newcreateModel')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
   $('document').ready(function(){
        var imgloading = ' <div class="loadingImg">جاري التحميل ... الرجاء الانتظار ...<img class="loadingImg" src="{{ asset('images/Loading.gif') }}" alt="loading"></div>';
        var client_pros_base = [];
        var client_pros = '';
        var client_walit = 0;
        var clientdata = {};

        function clone(obj){
            var obj2 = [];
            obj.forEach((d)=>{
                var dd = Object.assign({}, d)
                obj2.push(dd)
            })
            return obj2;
        }

        function showInv(){
            document.querySelectorAll('.showInv').forEach(elm => {
                elm.onclick = function (){
                    document.getElementById('showInvDat').innerHTML = imgloading;
                    var urlRef = '{{ route('gets.newgetinvoice') }}/'+this.dataset.invid;
                    console.log(urlRef)
                    $.ajax({
                        url: urlRef,
                        type: "get",
                        data:{invid:this.dataset.invid},
                        cache: false,
                        
                        success: function (response) { 
                            document.getElementById('showInvDat').innerHTML = response.data.strHTML
                        },
                        error: function (xhr) {
                            alert("Error: - " + xhr.status + " " + xhr.statusText);
                        }
                    });
                }
            })
        }

        $("#client_id").attr("data-live-search", true)
        $('select#client_id').selectpicker();
        document.getElementById('client_id').onchange = function (){
            document.getElementById('clientdata').innerHTML = imgloading;
            
            var client_id = this.value;
            var getUri = "{{ url('/dashboard/get/newgetdata') }}/" + client_id;
            $.ajax({
                url: getUri,
                type: 'GET',
                cache: false,
                success: function(data) {
                    document.getElementById('clientdata').innerHTML = data;

                    getUri += '/json';
                    $.ajax({
                        url: getUri,
                        type: 'GET',
                        cache: false,
                        success: function(data) {
                            console.log(data);
                            
                            clientdata = data
                            client_pros = [...data.inv_pro];
                            client_pros_base = clone(data.inv_pro)
                            client_walit = data.walit

                            setTimeout(() => {
                                var oldValue = ''
                                document.querySelectorAll('#getTable td input').forEach(elm => {
                                    elm.onfocus= function(){
                                        oldValue = (this.value)? this.value : 0;
                                    }

                                    elm.onchange=function(){
                                        
                                        var invproid = this.dataset.invproid
                                        var i=-1;
                                        row = client_pros.find((p)=>{ ++i; return p.invoice_product_id == this.dataset.invproid });
                                        if(this.dataset.type == 'get'){
                                            if((+this.value + +row.return_quantity) <= +row.get_quantity_next){
                                                this.value = +this.value
                                                row.get_quantity = +this.value;
                                                row.get_pay = (+this.value * (100 - +row.discount)/100 * +row.Public_Price).toFixed(2);
                                            }else{
                                                this.value = oldValue;
                                            }
                                        }
                                        
                                        if(this.dataset.type == 'return'){
                                            if((+this.value + +row.get_quantity) <= +row.get_quantity_next){
                                                this.value = +this.value
                                                row.return_quantity = +this.value;                                          
                                            }else{
                                                this.value = oldValue;
                                            }
                                        }

                                        row.get_pay_next = ((+row.get_quantity_next - (+row.get_quantity + +row.return_quantity)) * (100-row.discount)/100 * +row.Public_Price).toFixed(2);
                                        
                                        client_pros[i] = row
                                        document.getElementById(`val-pro${invproid}`).innerHTML = row.get_pay
                                        document.getElementById(`next-pro${invproid}`).innerHTML = row.get_pay_next
                                        
                                        var val_pro_total = 0;
                                        var next_pro_total = 0;
                                        client_pros.forEach((elm)=>{
                                            val_pro_total += +elm.get_pay
                                            next_pro_total += +elm.get_pay_next
                                        })

                                        document.getElementById(`val-pro-total`).innerHTML = val_pro_total;
                                        document.getElementById(`next-pro-total`).innerHTML = next_pro_total;
                                    }
                                });

                                function resetData(){
                                    client_pros = clone(client_pros_base)
                                    next_pro_total = 0;
                                    client_pros.forEach((celm)=>{
                                        document.getElementById(`get-pro${celm.invoice_product_id}`).value = 0;
                                        document.getElementById(`return-pro${celm.invoice_product_id}`).value = 0;
                                        document.getElementById(`val-pro${celm.invoice_product_id}`).innerHTML = 0;
                                        document.getElementById(`next-pro${celm.invoice_product_id}`).innerHTML = celm.get_pay_next;
                                        next_pro_total += +celm.get_pay_next
                                    });
                                    
                                    document.getElementById(`val-pro-total`).innerHTML = 0;
                                    document.getElementById('next-pro-total').innerHTML = next_pro_total;
                                    document.getElementById('clientWalitadd').innerHTML = 0;
                                    document.getElementById('client_pay').value = '';

                                }

                                document.querySelector('.is_client_pay').onchange=function(){
                                    if(this.checked){
                                        document.getElementById('div_client_pay').style.display = 'block';
                                        $('#getTable input').attr('disabled', 'true');
                                        resetData()
                                    }else{
                                        document.getElementById('div_client_pay').style.display = 'none'
                                        $('#getTable input').removeAttr('disabled');
                                        resetData()
                                    }
                                }
                                
                                document.getElementById('client_pay').onchange=function(){
                                    if(+this.value > +clientdata.next_pro_total){
                                        alert("لا يمكن دفع أكبر من مديونية العميل :" + clientdata.next_pro_total)
                                        return false;
                                    }
                                    var pay = +this.value + +client_walit;
                                    var next_pro_total = 0;
                                    var val_pro_total = 0;

                                    var i = -1;
                                    client_pros.forEach((elm)=>{
                                        i++;

                                        var priceUnit = +elm.Public_Price * (100 - +elm.discount)/100;
                                        units = parseInt(pay/priceUnit);
                                        
                                        if(units>= elm.get_quantity_next){
                                            _units = elm.get_quantity_next
                                            _unitsPrice = _units * priceUnit;
                                        }else{
                                            _units = units
                                            _unitsPrice = _units * priceUnit;
                                        }
                                        _unitsPriceNext = +client_pros_base[i].get_pay_next - +_unitsPrice
                                        pay -= _unitsPrice;

                                        document.getElementById(`get-pro${elm.invoice_product_id}`).value = _units;
                                        document.getElementById(`val-pro${elm.invoice_product_id}`).innerHTML = _unitsPrice.toFixed(2);
                                        document.getElementById(`next-pro${elm.invoice_product_id}`).innerHTML = _unitsPriceNext.toFixed(2);
                                        val_pro_total += +_unitsPrice
                                        next_pro_total += +_unitsPriceNext

                                        elm.return_quantity = 0;
                                        elm.get_quantity = _units;
                                        elm.get_pay = _unitsPrice;
                                        elm.get_pay_next = _unitsPriceNext;

                                        client_pros[i] = elm;
                                    });
                                        
                                    var clientWalitadd = pay
                                    document.getElementById(`val-pro-total`).innerHTML = val_pro_total.toFixed(2);
                                    document.getElementById('next-pro-total').innerHTML = next_pro_total.toFixed(2);
                                    document.getElementById('clientWalitadd').innerHTML = clientWalitadd.toFixed(2);
                                }

                                document.getElementById('is_client_payDiv').style.display ='block';

                                document.querySelectorAll('#getTable tbody th').forEach(th => {
                                    th.onclick=function(){
                                        console.log('clicked')
                                        // this.classList.toggle('bgGreen')
                                        if(this.classList.contains('bgGreen')){
                                            console.log('clicked111')
                                            this.classList.remove('bgGreen')
                                        }else{
                                            this.classList.add('bgGreen')
                                            console.log('clicked1112222')
                                        }
                                    }
                                });
                                /* ===============================
                                ||  Start: OnSubmit
                                ================================== */
                                document.getElementById('saveSubmit').style.display ='block';
                                document.getElementById('saveSubmit').onclick = function(evt){
                                    evt.preventDefault();
                                    // Inputs Validates
                                    var to_store_id = document.getElementById('to_store_id').value;
                                    var client_id = document.getElementById('client_id').value;

                                    if(!to_store_id){
                                        alert('من فضلك اختر مخزن لعمل التحصيل او المرتجع عليه،');
                                        return false;
                                    }

                                    $client_pay_text = document.getElementById('client_pay').value;
                                    if(($client_pay_text > 200 || $client_pay_text < 0 ) && +clientdata.next_pro_total <= +document.getElementById('next-pro-total').innerHTML){
                                        alert('يجب اختيار كميات أولا');
                                        return false;
                                    }

                                    
                                    if(!client_id){
                                        alert('اختر عميل اولا');
                                        return false;
                                    }

                                    // Divid to gets and returns
                                    console.log(clientdata)
                                    var inv_ids = clientdata.inv_ids
                                    var gets = {}
                                    var returns = {}
                                    
                                    var getsArr;
                                    var returnsArr;
                                    // inv_ids = Object.keys(inv_ids).map((key) => [Number(key), inv_ids[key]]);
                                    console.log("inv_ids============");
                                    console.log(inv_ids)
                                    // inv_ids.forEach(elm => {
                                    Object.keys(inv_ids).map((key)=>{ 
                                        elm = inv_ids[key]
                                        console.log(inv_ids[key])
                                        getsArr = client_pros.filter((c)=> {return (c.get_quantity > 0&& c.invoice_id == elm) })

                                        if(getsArr.length>0){
                                            gets[elm] = getsArr
                                        }

                                        returnsArr = client_pros.filter((c)=> {return (c.return_quantity > 0 && c.invoice_id == elm) })
                                        if(returnsArr.length>0){
                                            returns[elm] = returnsArr
                                        }
                                    });

                                    var client_pay = +document.getElementById('client_pay').value;
                                    var paidFromWalit = +clientdata.walit;
                                    var addToWalit = +clientdata.walit;

                                    var unitsTotalPrice = 0;
                                    client_pros.forEach(c => {
                                        var unitPrice = +c.Public_Price * (100 - +c.discount)/100;
                                        unitsTotalPrice += +c.get_quantity * +unitPrice;
                                    });

                                    var is_client_pay = false;
                                    if(document.getElementById('client_pay').value !== ''){
                                        is_client_pay =true
                                        console.log('client_pay: true')
                                        addToWalit = +client_pay + +clientdata.walit - +unitsTotalPrice;
                                    }
                                    

                                    clientWalitaddText = +document.getElementById('clientWalitadd').innerHTML;
                                    if(+addToWalit.toFixed(2) !== +clientWalitaddText.toFixed(2)){
                                        console.log(+addToWalit.toFixed(2), +clientWalitaddText.toFixed(2))
                                        alert('خطأ إدخال بيانات، حاول ادخال بيانات صحيحة')
                                        return false;
                                    }

                                    getsLength = Object.keys(gets).length
                                    returnsLength = Object.keys(returns).length
                                    var formSendData = {
                                        client_id:client_id,
                                        to_store_id:to_store_id,
                                        insert_date: document.getElementById('insert_date').value,
                                        paidFromWalit:paidFromWalit,
                                        addToWalit:addToWalit,
                                        getLength:getsLength,
                                        is_gets:getsLength>0? true : false ,
                                        gets:gets,
                                        returnLength:returnsLength,
                                        is_returns:returnsLength>0? true : false ,
                                        returns:returns,
                                        is_client_pay: is_client_pay,
                                        client_paid:  is_client_pay? client_pay : unitsTotalPrice,
                                    };


                                    
                                    //Post FormSendData To the server
                                    $("button#saveSubmit").after(imgloading)
                                    document.querySelector('button.saveSubmit').style.display = 'none';
                                    $.ajax({
                                        url: '{{ route('gets.storenewget') }}/'+formSendData.client_id,
                                        type: "post",
                                        data:formSendData,
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        cache: false,
                                        
                                        success: function (data) {
                                            document.querySelector('button.saveSubmit').style.display = 'block';
                                            $("#submitData .loadingImg").remove()
                                            $('#client_id option').removeAttr("selected");
                                            var bgColor = data.success? 'green' : 'red';
                                            
                                            $msg =  '<div class="alertMsg">';
                                            $msg += '   <div style="padding: 5px; text-align: center; color:#fff; background: '+ bgColor +'">'+ data.message +'</div>';
                                            

                                            if(data.success){
                                                $msg += '   <div style="margin-top: 20px; padding: 5px; text-align: center; color:#fff; background: blue">يمكنك إختيار عميل مرة أخري لعمل تحصيل/مرتجع جديد.</div>';
                                                $msg += '</div>';
                                                document.getElementById("clientdata").innerHTML = $msg;
                                            }else{
                                                $msg += '</div>';
                                                document.getElementById("errorMsg").innerHTML = $msg;
                                            }

                                            
                                            console.log(formSendData);
                                            console.log(data)  
                                        },
                                        error: function (xhr) {
                                            document.querySelector('button.saveSubmit').style.display = 'block';
                                            $("#submitData .loadingImg").remove()
                                            alert("Error: - " + xhr.status + " " + xhr.statusText);
                                        }
                                    });


                                }
                                /* End: OnSubmit */ 
                            }, 50);

                            setTimeout(() => {
                                showInv();
                            }, 50);
                            
                        },
                        error: function(xhr) {
                            console.log(xhr.status + ' ' + xhr.statusText);
                        }
                    });

                },
                error: function(xhr) {
                    console.log(xhr.status + ' ' + xhr.statusText);
                }
            });
            
            
        }

        
        
   })
</script>
@endsection


