@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Index Collections Outpatient</h1>

    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <button class="btn btn-sm btn-outline-secondary">Share</button>
        <button class="btn btn-sm btn-outline-secondary">Export</button>
      </div>
      <button id="week" class="btn btn-sm btn-outline-secondary dropdown-toggle">
        <span data-feather="calendar"></span>
          This week
      </button>
    </div>
  </div>


  <div class="row">
    <a class="btn btn-sm btn-primary" href="{{ route('collections.outpatient.create',['' => Auth::user()->id]) }}">Create</a>

  </div>

  <br />

  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

  <table id="outpatient_payment_table" class="table table-sm table-striped" style="width:100%">
    <thead>
      <!-- Table Row -->
      <tr>
        <!-- Table Header -->
        <th>O.R. Date</th>
        <th>O.R. No.</th>
        <th>Patient Name</th>
        <th>Discount (%)</th>
        <th>Amount Paid</th>
        <th>Cashier On-duty</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($payments as $payment)
      <tr>
        <td>{{ $payment->or_date }}</td>
        <td>{{ $payment->or_no_prefix }}</td>
        <td>{{ $payment->patient_name }}</td>
        <td>{{ $payment->discount }}</td>
        <td>{{ $payment->amount_paid }}</td>
        <td>{{ $payment->employee_name }}</td>
        <td>{{ $payment->status }}</td>
        <td><a href="{{ route('collections.outpatient.print.pdf', ['' => $payment->or_no_prefix ]) }}" class="btn btn-sm btn-outline-danger">Print Receipt</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>




  <!-- <script>
    $(document).ready(function() {
      $('#outpatient_payment_table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ route('collections.outpatient.getdata') }}",
        "columns": [
          {"data": "or_date"},
          {"data": "or_no_prefix"},
          {"data": "patient_name"},
          {"data": "discount"},
          {"data": "amount_paid"},
          {"data": "employee_name"},
          {"data": "status"},
          {"data": "action", orderable:false, searchable: false }
        ]
      });

    });
  </script> -->

  <script type="text/javascript">
 
    $(document).on('click', '.print', function(){
       var ids = $(this).attr('id');

      alert('clicked print receipt button');
      alert(ids);

      $.ajax({
        type: "POST",
        url: "/collections/outpatient/print/pdf",
        data: { id:ids },
        dataType: "JSON",
        success: function(data) {
          console.log(data.id);

        }
      });
    });

    $(document).on('click', '.edit', function(){
      alert('clicked edit button');

    });
  </script>

</main>


@endsection