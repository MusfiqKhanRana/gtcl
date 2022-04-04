<div class="tab-pane" id="cleaned">
    <div class="portlet-title">
        <div class="caption">
            <b>Cleaned List</b>
        </div>
        <div class="tools"> </div>
    </div>
    <hr>
    <table class="table table-striped table-bordered table-hover" id="cleaned_table">
        <thead>
            <tr>
                <th>
                    Invoice No.
                </th>
                <th>
                    Item Name
                </th>
                <th>
                    Grade
                </th>
                <th>
                    Quantity
                </th>
                <th>
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    11111
                </td>
                <td>
                    Rui
                </td>
                <td>
                    300-500gm
                </td>
                <td>
                    60kg
                </td>
                <td>
                    <button style="margin-bottom:3px" data-toggle="modal" href="#cleanedProcessingDataModal" class="btn btn-success"><i class="fa fa-refresh" aria-hidden="true"></i> Processing Data</button>
                    <button style="margin-bottom:3px" data-toggle="modal" href="#cleanedcleangModal" class="btn btn-warning"><i class="fa fa-refresh" aria-hidden="true"></i> Clean</button>
                    <button style="margin-bottom:3px" data-toggle="modal" href="#cleanedGradingModal" class="btn btn-primary"><i class="fa fa-refresh" aria-hidden="true"></i> Grading</button>
                    <button style="margin-bottom:3px" data-toggle="modal" href="#cleanedGlazingModal" class="btn btn-info"><i class="fa fa-refresh" aria-hidden="true"></i> Glazing</button>
                    <button style="margin-bottom:3px" data-toggle="modal" href="#cleanedReturnModal" class="btn btn-danger"><i class="fa fa-repeat" aria-hidden="true"></i> Return & Wastage</button>
                </td>
            </tr>
        </tbody>
    </table>
    @include('backend.production.processing.iqf.cleaned.cleanedProcessingDataModal')
    @include('backend.production.processing.iqf.cleaned.cleanedGradingModal')
    @include('backend.production.processing.iqf.cleaned.cleanedcleangModal')
    @include('backend.production.processing.iqf.cleaned.cleanedGlazingModal')
    @include('backend.production.processing.iqf.cleaned.cleanedReturnModal')
</div>