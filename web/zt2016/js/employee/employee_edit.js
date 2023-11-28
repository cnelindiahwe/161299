$(document).ready(function() {
    var estimatselect = {};
        // estimate add feild
    $('.family .add-more').click(function(){
        var addTable=`  <div class="family-feild">  <div class="col-sm-3 col-sm-12">
        <label for="familyName">Name:</label>
        <input type="text" name="familyName[]"  id=familyName" size="3" class="form-control" />

    </div>
    <div class="col-sm-3 col-sm-12">
        <label for="familyRelationship">Relationship:</label>
        <input type="text" name="familyRelationship[]"  id="familyRelationship" size="3" class="form-control" />

    </div>
    <div class="col-sm-2 col-sm-12">
        <label for="familydob">Date of Birth :</label>
        <input type="text" name="familydob[]"  id="familydob" size="3" class="form-control floating datetimepicker" />

    </div>
    <div class="col-sm-2 col-sm-12">
        <label for="familyPhone">Phone:</label>
        <input type="text" name="familyPhone[]"  id="familyPhone" size="3" class="form-control" />

    </div>
    <div class="col-sm-2 col-sm-12 d-flex" style="height:60px;">
        <div class="add-more mt-auto">
        <a href="javascript:void(0)" class="text-danger font-18 remove-felid" title="Remove"><i class="fa fa-trash-o"></i></a>
        </div>
    </div>
    </div>`;
    $('.family').append(addTable);
    $('.datetimepicker').datetimepicker({
            format: 'DD/MM/YYYY',
            icons: {
                up: "fa fa-angle-up",
                down: "fa fa-angle-down",
                next: 'fa fa-angle-right',
                previous: 'fa fa-angle-left'
            }
        });

        
    });

    $('.education .add-more').click(function(){
        var addTable=`  <div class="education-field">
        <div class="col-sm-2 col-sm-12">
            <label for="Institution">Institution:</label>
            <input type="text" name="Institution[]"  id=Institution" size="3" class="form-control" />

        </div>
        <div class="col-sm-2 col-sm-12">
            <label for="Subject">Subject:</label>
            <input type="text" name="Subject[]"  id="Subject" size="3" class="form-control" />

        </div>
        <div class="col-sm-2 col-sm-12">
            <label for="familydob">Year :</label>
            <input type="text" name="year[]"  id="year" size="3" class="form-control" />

        </div>
        <div class="col-sm-2 col-sm-12">
            <label for="Degree">Degree :</label>
            <input type="text" name="Degree[]"  id="Degree" size="3" class="form-control" />

        </div>
        <div class="col-sm-2 col-sm-12">
            <label for="Grade">Grade :</label>
            <input type="text" name="Grade[]"  id="Grade" size="3" class="form-control" />

        </div>
    
        <div class="col-sm-2 col-sm-12 d-flex" style="height:61px;">
            <div class="add-more mt-auto">
            <a href="javascript:void(0)" class="text-danger font-18 remove-felid" title="Remove"><i class="fa fa-trash-o"></i></a>
            </div>
        </div>
    </div>`;
    $('.education').append(addTable);


        
    });
    $('.experience .add-more').click(function(){
        var addTable=`  <div class="experience-field">
        <div class="col-sm-2 col-sm-12">
            <label for="CompanyName">Company Name:</label>
            <input type="text" name="CompanyName[]"  id=CompanyName" size="3" class="form-control" />

        </div>
        <div class="col-sm-2 col-sm-12">
            <label for="Location">Location:</label>
            <input type="text" name="Location[]"  id="Location" size="3" class="form-control" />

        </div>
        <div class="col-sm-2 col-sm-12">
            <label for="JobPosition">Job Position :</label>
            <input type="text" name="JobPosition[]"  id="JobPosition" size="3" class="form-control" />

        </div>
        <div class="col-sm-2 col-sm-12">
            <label for="PeriodFrom">Period From :</label>
            <input type="text" name="PeriodFrom[]"  id="PeriodFrom" size="3" class="form-control floating datetimepicker" />

        </div>
        <div class="col-sm-2 col-sm-12">
            <label for="PeriodTo">Period To :</label>
            <input type="text" name="PeriodTo[]"  id="PeriodTo" size="3" class="form-control floating datetimepicker" />

        </div>

        <div class="col-sm-2 col-sm-12 d-flex" style="height:61px;">
        <div class="add-more mt-auto">
        <a href="javascript:void(0)" class="text-danger font-18 remove-felid" title="Remove"><i class="fa fa-trash-o"></i></a>
        </div>
    </div>
    </div>`;
    $('.experience').append(addTable);
    $('.datetimepicker').datetimepicker({
        format: 'DD/MM/YYYY',
        icons: {
            up: "fa fa-angle-up",
            down: "fa fa-angle-down",
            next: 'fa fa-angle-right',
            previous: 'fa fa-angle-left'
        }
    });

        
    });
    

    // $('.datetimepicker').datetimepicker({
    //     format: 'DD/MM/YYYY',
    //     icons: {
    //         up: "fa fa-angle-up",
    //         down: "fa fa-angle-down",
    //         next: 'fa fa-angle-right',
    //         previous: 'fa fa-angle-left'
    //     }
    // });
    
        let estimat_data = {'grandtotal':'','newslides':0,'editslides':0,'hoursum':0};
       
    
    
    
        //estimate remove feild
    $('.family').on('click', '.remove-felid', function() {
        $(this).closest('.family-feild').remove();
    });
    $('.education').on('click', '.remove-felid', function() {
        $(this).closest('.education-field').remove();
    });
    $('.experience').on('click', '.remove-felid', function() {
        $(this).closest('.experience-field').remove();
    });
    

    
    
    }); 