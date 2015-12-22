@extends('admin._master')

@section('page-toolbar')

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/core/third_party/jquery-nestable/jquery.nestable.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/menu-nestable.css') }}">
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('assets/admin/core/third_party/jquery-nestable/jquery.nestable.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/js/pages/menu-nestable.js') }}"></script>
@endsection

@section('js-init')
    <script type="text/javascript">
        $(window).load(function() {
            MenuNestable.init();
            MenuNestable.handleNestableMenu();
        });
    </script>
@endsection

@section('content')
    <form action="{{ asset($adminCpAccess.'/menus/edit/'.$object->id) }}" accept-charset="utf-8" method="POST" class="form-save-menu">
        {{ csrf_field() }}
        <input type="hidden" name="deleted_nodes" value="">
        <textarea name="menu_nodes" id="nestable-output" class="form-control hidden" style="display: none !important;"></textarea>
        <div class="row">
            <div class="col-md-12">
                <div class="note note-danger">
                    <p><label class="label label-danger">NOTE</label> You need to enable javascript.</p>
                </div>
                <div class="note note-danger">
                    <p><label class="label label-danger">NOTE</label> <b>Edit menu structure</b> is supported in
                        Firefox, Chrome, Opera, Safari, Internet Explorer 10 and Internet Explorer 9 only. Internet
                        Explorer 8 and older not supported.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-note font-dark"></i>
                            <span class="caption-subject font-dark sbold uppercase">Basic information</span>
                        </div>
                        <div class="actions">
                            <div class="btn-group btn-group-devided">
                                <button class="btn btn-transparent btn-success btn-circle btn-sm active" type="submit">
                                    <i class="fa fa-check"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="form-group">
                            <label>Menu title</label>
                            <input type="text" name="title" class="form-control" value="{{ $object->title }}">
                        </div>
                        <div class="form-group">
                            <label>Menu name</label>
                            <input type="text" name="slug" class="form-control" value="{{ $object->slug }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-link font-dark"></i>
                            <span class="caption-subject font-dark sbold uppercase">Add link</span>
                        </div>
                        <div class="tools">
                            <a href="" class="collapse" data-original-title="" title=""> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="box-links-for-menu">
                            <div id="external_link" class="tab-pane active">
                                <div class="form-group">
                                    <label for="node-title" class="">Title</label>
                                    <input type="text" required="" class="form-control" id="node-title" placeholder="" value="" name="" autocomplete="false">
                                </div>
                                <div class="form-group">
                                    <label for="node-url" class="">Url</label>
                                    <input type="text" required="" class="form-control" id="node-url" placeholder="http://" value="" name="" autocomplete="false">
                                </div>
                                <div class="form-group">
                                    <label for="node-css" class="">CSS class</label>
                                    <input type="text" required="" class="form-control" id="node-css" placeholder="" value="" name="" autocomplete="false">
                                </div>
                                <div class="text-right">
                                    <div class="btn-group btn-group-devided">
                                        <a href="#" title="" class="btn-add-to-menu btn btn-success btn-sm btn-circle btn-transparent active"><span class="text"><i class="fa fa-plus"></i> Add to menu</span></a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-bars font-dark"></i>
                            <span class="caption-subject font-dark sbold uppercase">Menu structure</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="dd nestable-menu" id="nestable" data-depth="0">
                            {!! $nestableMenuSrc !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection