<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>W2UI Demo: grid-10</title>
	<link rel="stylesheet" type="text/css" href="//w2ui.com/src/w2ui-1.3.min.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
	<script type="text/javascript" src="//w2ui.com/src/w2ui-1.3.min.js"></script>
    </head>
    <body>
        <div id="grid" style="width: 100%; height: 250px;"></div>
        <br>
        <input type="button" value="Select All" onclick="w2ui.grid.selectAll();">
        <input type="button" value="Select None" onclick="w2ui.grid.selectNone();">
        <input type="button" value="Select a Record" onclick="w2ui.grid.select(5);">
        <input type="button" value="Deselect a Record" onclick="w2ui.grid.unselect(5);">
        <input type="button" value="Select Several" onclick="w2ui.grid.select(5,3,7);">
        <input type="button" value="Get Several" onclick="alert(w2ui.grid.getSelection());">
        
        <script type="text/javascript">
        $(function(){
           $('#grid').w2grid({
              name:'grid',
              show:{
                  selectColumn: true
              },
              columns:[
                  {field:'recid',caption:'ID',size:'30px',sortable: true,attr:'align="center"'},
                  {field:'lname',caption:'Last Name',size:'30%',sortable: true},
                  {field:'fname',caption:'First Name',size:'30%',sortable: true},
                  {field:'email',caption:'Email',size:'40%',sortable: true},
                  {field:'sdate',caption:'Start Date',size:'90px',sortable: true}
              ],
              records: [
			{ recid: 1, fname: 'John', lname: 'doe', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
			{ recid: 2, fname: 'Stuart', lname: 'Motzart', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
			{ recid: 3, fname: 'Jin', lname: 'Franson', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
			{ recid: 4, fname: 'Susan', lname: 'Ottie', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
			{ recid: 5, fname: 'Kelly', lname: 'Silver', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
			{ recid: 6, fname: 'Francis', lname: 'Gatos', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
			{ recid: 7, fname: 'Mark', lname: 'Welldo', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
			{ recid: 8, fname: 'Thomas', lname: 'Bahh', email: 'jdoe@gmail.com', sdate: '4/3/2012' }
	      ]
           }); 
        });
        </script>
    </body>
</html>
