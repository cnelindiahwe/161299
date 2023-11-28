<?php
 $this->load->view('admin_temp/header');

?>
 <div class="content container-fluid">
        
            <div class="row">
                <div class="col-sm-12">
                    <div class="file-wrap">
                        <div class="file-sidebar">
                            <div class="file-header justify-content-center">
                                <span>Projects</span>
                                <a href="javascript:void(0);" class="file-side-close"><i class="fa fa-times"></i></a>
                            </div>
                            <form class="file-search">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <i class="fa fa-search"></i>
                                    </div>
                                    <input type="text" class="form-control rounded-pill" placeholder="Search">
                                </div>
                            </form>
                            <div class="file-pro-list">
                                <div class="file-scroll">
                                    <ul class="file-menu">
                                        <li class="active">
                                            <a href="#">All Projects</a>
                                        </li>
                                        <li>
                                            <a href="#">Office Management</a>
                                        </li>
                                        <li>
                                            <a href="#">Video Calling App</a>
                                        </li>
                                        <li>
                                            <a href="#">Hospital Administration</a>
                                        </li>
                                        <li>
                                            <a href="#">Virtual Host</a>
                                        </li>
                                    </ul>
                                    <div class="show-more">
                                        <a href="#">Show More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="file-cont-wrap d">
                            <div class="file-cont-inner">
                                <div class="file-cont-header">
                                    <div class="file-options">
                                        <a href="javascript:void(0)" id="file_sidebar_toggle" class="file-sidebar-toggle">
                                            <i class="fa fa-bars"></i>
                                        </a>
                                    </div>
                                    <span>File Manager</span>
                                    <div class="file-options">
                                        <span class="btn-file"><input type="file" class="upload"><i class="fa fa-upload"></i></span>
                                    </div>
                                </div>
                                <div class="file-content">
                                    <form class="file-search">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <i class="fa fa-search"></i>
                                            </div>
                                            <input type="text" class="form-control rounded-pill" placeholder="Search">
                                        </div>
                                    </form>
                                    <div class="file-body">
                                        <div class="file-scroll">
                                            <div class="file-content-inner">
                                                <div class="row row-sm">
                                                    <div class="col-6 col-sm-4 col-md-3 col-lg-4 col-xl-3">
                                                        <div class="card card-file">
                                                            <div class="dropdown-file">
                                                                <a href="" class="dropdown-link" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a href="#" class="dropdown-item">View Details</a>
                                                                    <a href="#" class="dropdown-item">Share</a>
                                                                    <a href="#" class="dropdown-item">Download</a>
                                                                    <a href="#" class="dropdown-item">Rename</a>
                                                                    <a href="#" class="dropdown-item">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="card-file-thumb">
                                                                <i class="fa fa-file-pdf-o"></i>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6><a href="">Sample.pdf</a></h6>
                                                                <span>10.45kb</span>
                                                            </div>
                                                            <div class="card-footer">
                                                                <span class="d-none d-sm-inline">Last Modified: </span>1 min ago
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-6 col-sm-4 col-md-3 col-lg-4 col-xl-3">
                                                        <div class="card card-file">
                                                            <div class="dropdown-file">
                                                                <a href="" class="dropdown-link" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a href="#" class="dropdown-item">View Details</a>
                                                                    <a href="#" class="dropdown-item">Share</a>
                                                                    <a href="#" class="dropdown-item">Download</a>
                                                                    <a href="#" class="dropdown-item">Rename</a>
                                                                    <a href="#" class="dropdown-item">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="card-file-thumb">
                                                                <i class="fa fa-file-word-o"></i>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6><a href="">Document.docx</a></h6>
                                                                <span>22.67kb</span>
                                                            </div>
                                                            <div class="card-footer">
                                                                <span class="d-none d-sm-inline">Last Modified: </span>30 mins ago
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-6 col-sm-4 col-md-3 col-lg-4 col-xl-3">
                                                        <div class="card card-file">
                                                            <div class="dropdown-file">
                                                                <a href="" class="dropdown-link" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a href="#" class="dropdown-item">View Details</a>
                                                                    <a href="#" class="dropdown-item">Share</a>
                                                                    <a href="#" class="dropdown-item">Download</a>
                                                                    <a href="#" class="dropdown-item">Rename</a>
                                                                    <a href="#" class="dropdown-item">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="card-file-thumb">
                                                                <i class="fa fa-file-image-o"></i>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6><a href="">icon.png</a></h6>
                                                                <span>12.47kb</span>
                                                            </div>
                                                            <div class="card-footer">
                                                                <span class="d-none d-sm-inline">Last Modified: </span>1 hour ago
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-6 col-sm-4 col-md-3 col-lg-4 col-xl-3">
                                                        <div class="card card-file">
                                                            <div class="dropdown-file">
                                                                <a href="" class="dropdown-link" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a href="#" class="dropdown-item">View Details</a>
                                                                    <a href="#" class="dropdown-item">Share</a>
                                                                    <a href="#" class="dropdown-item">Download</a>
                                                                    <a href="#" class="dropdown-item">Rename</a>
                                                                    <a href="#" class="dropdown-item">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="card-file-thumb">
                                                                <i class="fa fa-file-excel-o"></i>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6><a href="">Users.xls</a></h6>
                                                                <span>35.11kb</span>
                                                            </div>
                                                            <div class="card-footer">23 Jul 6:30 PM</div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <h4>Files</h4>
                                                <div class="row row-sm">
                                                    <div class="col-6 col-sm-4 col-md-3 col-lg-4 col-xl-3">
                                                        <div class="card card-file">
                                                            <div class="dropdown-file">
                                                                <a href="" class="dropdown-link" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a href="#" class="dropdown-item">View Details</a>
                                                                    <a href="#" class="dropdown-item">Share</a>
                                                                    <a href="#" class="dropdown-item">Download</a>
                                                                    <a href="#" class="dropdown-item">Rename</a>
                                                                    <a href="#" class="dropdown-item">Delete</a>
                                                                </div>
                                                            </div>
                                                            <div class="card-file-thumb">
                                                                <i class="fa fa-file-word-o"></i>
                                                            </div>
                                                            <div class="card-body">
                                                                <h6><a href="">Updates.docx</a></h6>
                                                                <span>12mb</span>
                                                            </div>
                                                            <div class="card-footer">9 Aug 1:17 PM</div>
                                                        </div>
                                                    </div>
                                                    
                                                   

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <?php
 $this->load->view('admin_temp/footer');

?>