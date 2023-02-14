<div class="col-md-4 viewDiv">
    <div class="form-group"> 
        <label for="view">إختر نوع الجرد<span class="astrik">*</span></label>  
        <select required="" id="view" class="form-control store_id" name="view">
            <option value="">إختر نوع الجرد *</option>
            <option value="stock" {{ ($view=="stock")? "selected" : "" }} >جرد سريع</option>
            <option value="stocknorunid" {{ ($view=="stocknorunid")? "selected" : "" }}>جرد سريع بدون رقم تشغيلة</option>
            <option value="stocksql" {{ ($view=="stocksql")? "selected" : "" }}>جرد SQL </option>                                    
        </select>
    </div>
</div>

{!! select([
    'errors' => $errors,
    'edit' => $store_id,
    'name' => 'store_id',
    'frkName' => 'Store_Name',
    'rows' => $stores,
    'transval' => 'إختر مخزن لجرده',
    'label' => true,
    'required' => 'required',
    'cols' => 4,
]) !!}

<div class="col-md-4">
    <a class="btn btn-md btn-info" id="runstock"
        style="color: #fff; line-height: 30px; display: block; width: 200px; height: 55px; margin-top: 28px;">جرد/بحث</a>
</div>