<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>W2UI Demo: grid-9</title>
	<link rel="stylesheet" type="text/css" href="//w2ui.com/src/w2ui-1.3.min.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
	<script type="text/javascript" src="//w2ui.com/src/w2ui-1.3.min.js"></script>
    </head>
    <body>
        <div id="grid" style="width: 100%;height: 400px;"></div>
        <br>
        <input type="button" value="Add One Record" onclick="addARecord();">
        <input type="button" value="Add Multiple Records" onclick="addMRecord();">
        <input type="button" value="Remove All Added Records" onclick="removeRecords();">
        
        <script type="text/javascript">
            $(function(){
               $('#grid').w2grid({
                   name:'grid',
                   show:{
                     lineNumber: true,
                     footer: true
                   },
                   columns:[
                       {field:'recid',caption:'RecID',size:'60px',sortable: true},
                       {field:'lname',caption:'Last Name',size:'30%'},
                       {field:'fname',caption:'First Name',size:'30%'},
                       {field:'email',caption:'Email',size:'40%'},
                       {field:'sdate',caption:'Start Date',size:'90px'},
                   ],
                   records:[
                       { recid: 1, fname: 'John', lname: 'doe', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		       { recid: 2, fname: 'Stuart', lname: 'Motzart', email: 'jdoe@gmail.com', sdate: '4/3/2012' }
                   ]
               }) ;
            });
            function addARecord(){
                var g= w2ui['grid'].records.length;
                w2ui['grid'].add({recid: g+1,fname:'Jin',lname:'Franson',email:'jdoe@gmail.com',sdate:'4/3/2012'});
            }
            function addMRecord() {
                var g = w2ui['grid'].records.length;
                w2ui['grid'].add([
		{ recid: g + 1, fname: 'Susan', lname: 'Ottie', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 2, fname: 'Kelly', lname: 'Silver', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 3, fname: 'Francis', lname: 'Gatos', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 4, fname: 'Mark', lname: 'Welldo', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 5, fname: 'Thomas', lname: 'Bahh', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 6, fname: 'Susan', lname: 'Ottie', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 7, fname: 'Kelly', lname: 'Silver', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 8, fname: 'Francis', lname: 'Gatos', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 9, fname: 'Mark', lname: 'Welldo', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 10, fname: 'Thomas', lname: 'Bahh', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 11, fname: 'Susan', lname: 'Ottie', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 12, fname: 'Kelly', lname: 'Silver', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 13, fname: 'Francis', lname: 'Gatos', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 14, fname: 'Mark', lname: 'Welldo', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 15, fname: 'Thomas', lname: 'Bahh', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 16, fname: 'Susan', lname: 'Ottie', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 17, fname: 'Kelly', lname: 'Silver', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 18, fname: 'Francis', lname: 'Gatos', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 19, fname: 'Mark', lname: 'Welldo', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 20, fname: 'Thomas', lname: 'Bahh', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 21, fname: 'Susan', lname: 'Ottie', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 22, fname: 'Kelly', lname: 'Silver', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 23, fname: 'Francis', lname: 'Gatos', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 24, fname: 'Mark', lname: 'Welldo', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
		{ recid: g + 25, fname: 'Thomas', lname: 'Bahh', email: 'jdoe@gmail.com', sdate: '4/3/2012' }
                ]);
            }
            
           function removeRecords() {
                w2ui.grid.clear();
                w2ui.grid.records = [
                        { recid: 1, fname: 'John', lname: 'doe', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
                        { recid: 2, fname: 'Stuart', lname: 'Motzart', email: 'jdoe@gmail.com', sdate: '4/3/2012' }
                ];
                w2ui.grid.total = 2;
                w2ui.grid.refresh();    
           }
           </script>
    </body>
</html>
