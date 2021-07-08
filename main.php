 <!-- Main content -->
 <div class="content">
     <div class="container-fluid">
         <div class="row">
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-info">
                     <div class="inner">
                         <h3><?php countrecords("students"); ?></h3>

                         <p>Students</p>

                     </div>
                     <div class="icon">
                         <i class="fas fa-users"></i>
                     </div>
                     <a href="students_view.php" class="small-box-footer">
                         More info <i class="fa fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-success">
                     <div class="inner">
                         <h3><?php countrecords("feescollection"); ?><sup style="font-size: 20px"></sup></h3>

                         <p>Fees Collection</p>

                     </div>
                     <div class="icon">
                         <i class="fas fa-wallet"></i>
                     </div>
                     <a href="feescollection_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-warning">
                     <div class="inner">
                         <h3><?php countrecords("branch"); ?></h3>

                         <p>Banks</p>

                     </div>
                     <div class="icon">
                         <i class="fa fa-university"></i>
                     </div>
                     <a href="branch_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-danger">
                     <div class="inner">
                         <h3><?php countrecords("subjects"); ?></h3>

                         <p>Subjects</p>

                     </div>
                     <div class="icon">
                         <i class="fa fa-book"></i>
                     </div>
                     <a href="subjects_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-primary">
                     <div class="inner">
                         <h3> <?php countrecords("teachers"); ?></h3>

                         <p>Teachers</p>

                     </div>
                     <div class="icon">
                         <i class="fa fa-id-badge"></i>
                     </div>
                     <a href="teachers_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-secondary">
                     <div class="inner">
                         <h3> <?php countrecords("classes"); ?></h3>

                         <p>Classes</p>

                     </div>
                     <div class="icon">
                         <i class="fa fa-graduation-cap"></i>
                     </div>
                     <a href="classes_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box" style="background-color:violet;">
                     <div class="inner">
                         <h3><?php countrecords("streams"); ?></h3>

                         <p>Streams</p>

                     </div>
                     <div class="icon">
                         <i class="fa fa-file-video"></i>
                     </div>
                     <a href="streams_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-dark">
                     <div class="inner">
                         <h3> <?php countrecords("hostels"); ?></h3>

                         <p>Hostels</p>

                     </div>
                     <div class="icon">
                         <i class="fa fa-building"></i>
                     </div>
                     <a href="hostels_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-success">
                     <div class="inner">
                         <h3> <?php countrecords("timetable"); ?></h3>

                         <p>Timetables</p>

                     </div>
                     <div class="icon">
                         <i class="fa fa-table"></i>
                     </div>
                     <a href="timetable_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-warning">
                     <div class="inner">
                         <h3><?php countrecords("events"); ?></h3>

                         <p>Events</p>

                     </div>
                     <div class="icon">
                         <i class="fas fa-calendar"></i>
                     </div>
                     <a href="events_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-danger">
                     <div class="inner">
                         <h3> <?php countrecords("notices"); ?></h3>

                         <p>Notices</p>
                     </div>
                     <div class="icon">
                         <i class="fa fa-info-circle"></i>
                     </div>
                     <a href="notices_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-6">
                 <!-- small card -->
                 <div class="small-box bg-info">
                     <div class="inner">
                         <h3> <?php countrecords("examresults"); ?></h3>
                         <p>Exam Results</p>
                     </div>
                     <div class="icon">
                         <i class="fa fa-folder"></i>
                     </div>
                     <a href="examresults_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>
             </div>
             <!-- ./col -->

             <div class="col-md-12">
                 <div class="card">
                     <div class="header">
                         <h4 class="title">Recent Fee Collection</h4>
                         <p class="category">Fees collection by date</p>
                     </div>
                     <div class="content table-responsive table-full-width">
                         <table class="table table-striped">
                             <thead>
                                 <th>ID</th>
                                 <th>Name</th>
                                 <th>Amount</th>
                                 <th>Balance</th>
                             </thead>
                             <tbody>
                                 <?php getrecentfees("feescollection"); ?>

                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-sm-6">
                 <!-- small card -->
                 <div class="small-box bg-success">
                     <div class="inner">
                         <h3> <?php countrecords("parents"); ?></h3>
                         <p>Parents</p>
                     </div>
                     <div class="icon">
                         <i class="fa fa-user"></i>
                     </div>
                     <a href="parents_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>

             </div>

             <!-- ./col -->
             <div class="col-lg-3 col-sm-6">
                 <!-- small card -->
                 <div class="small-box bg-info">
                     <div class="inner">
                         <h3> <?php countrecords("examcategories"); ?></h3>
                         <p>Exams</p>
                     </div>
                     <div class="icon">
                         <i class="fa fa-folder"></i>
                     </div>
                     <a href="examcategories_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>

             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-sm-6">
                 <!-- small card -->
                 <div class="small-box bg-secondary">
                     <div class="inner">
                         <h3> <?php countrecords("sessions"); ?></h3>
                         <p>Sessions</p>
                     </div>
                     <div class="icon">
                         <i class="fa fa-key"></i>
                     </div>
                     <a href="sessions_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>

             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-sm-6">
                 <!-- small card -->
                 <div class="small-box bg-danger">
                     <div class="inner">
                         <h3> <?php countrecords("classattendance"); ?></h3>
                         <p>Attendance</p>
                     </div>
                     <div class="icon">
                         <i class="ion ion-stats-bars"></i>
                         
                     </div>
                     <a href="classattendance_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>

             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-sm-6">
                 <!-- small card -->
                 <div class="small-box " style="background-color:teal;">
                     <div class="inner">
                         <h3> <?php countrecords("schoolmoney"); ?></h3>
                         <p>Fee Structure</p>
                     </div>
                     <div class="icon">
                         <i class="fa fa-list-alt"></i>
                     </div>
                     <a href="schoolmoney_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>

             </div>
             <!-- ./col -->
             <div class="col-lg-3 col-sm-6">
                 <!-- small card -->
                 <div class="small-box" style="background-color:orange;">
                     <div class="inner">
                         <h3> <?php countrecords("studentcategories"); ?></h3>
                         <p>Student Types</p>
                     </div>
                     <div class="icon">
                         <i class="fa fa-server"></i>
                     </div>
                     <a href="studentcategories_view.php" class="small-box-footer">
                         More info <i class="fas fa-arrow-circle-right"></i>
                     </a>
                 </div>

             </div>



             <!--row ends-->



             <!-- /.row -->
         </div>
         <!-- /.container-fluid -->
     </div>
     <!-- /.content -->
 </div>
 <!-- /.content-wrapper -->
 </div>
 <!-- Control Sidebar -->
 <aside class="control-sidebar control-sidebar-dark">
     <!-- Control sidebar content goes here -->
 </aside>
 <!-- /.control-sidebar -->





 <!-- jQuery -->
 <script src="plugins/jquery/jquery.min.js"></script>
 <!-- Bootstrap -->
 <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
 <!-- AdminLTE -->
 <script src="dist/js/adminlte.js"></script>

 <!-- OPTIONAL SCRIPTS -->
 <script src="plugins/chart.js/Chart.min.js"></script>
 <!-- AdminLTE for demo purposes -->
 <script src="dist/js/demo.js"></script>
 <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
 <script src="dist/js/pages/dashboard3.js"></script>
 </body>

 </html>