<x-app-layout>

    <!-- SAAN NTO?
    <script>
                (function () {
            // hold onto the drop down menu
            var dropdownMenu;

            // and when you show it, move it to the body
            $(window).on('show.bs.dropdown', function (e) {
                dropdownMenu = $(e.target).find('.dropdown-menu');
                console.log(dropdownMenu[0]._prevClass);
                $('body').append(dropdownMenu.detach());
                var eOffset = $(e.target).offset();
                if (dropdownMenu[0]._prevClass =="dropdown-menu nav" ) {

                    dropdownMenu.css({
                    'display': 'block',
                        'top': eOffset.top + $(e.target).outerHeight(),
                        'left': eOffset.left- 175
                });
                }else{
                    dropdownMenu.css({
                    'display': 'block',
                        'top': eOffset.top + $(e.target).outerHeight(),
                        'left': eOffset.left
                });
                }

            });

            $(window).on('hide.bs.dropdown', function (e) {
                $(e.target).append(dropdownMenu.detach());
                dropdownMenu.hide();
            });
        })();
    </script>-->
    <!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
    @if(session('deleted'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-start',
                title: 'Deleted!',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @elseif(session('updated'))
    <script>
            Swal.fire({
                toast: true,
                position: 'top-start',
                title: 'Updated!',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif
    <div class="py-3 container-fluid">
        <a href="/forward">
            <button class="btn btn-primary">Forward Vouchers</button>
        </a>
    </div>
    <div class="container-fluid">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            @if (in_array(Auth::user()->role, ['Accounting Receiving Clerk', 'admin', 'Budget Officer']))
            <div class="bg-gray dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" style="color: green">
                   <table>
                        <thead>
                            <tr>
                                <th colspan="4" class="text-center">ENCODE RECEIVED VOUCHERS (TO DO: MOVE UPDATING OF VOUCHERS)</th>
                            </tr>
                            <tr>
                                <th class="text-center">ACTION</th>
                                <th class="text-center">ORSNO</th>
                                <th class="text-center">UACS</th>
                                <th class="text-center">GROSS AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <form method="post" action="/save">@CSRF
                                    <input type="hidden" name="id" id="voucher-id">
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center; ">
                                        <button class="btn btn-primary">Submit</button>
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">
                                        <input required class="container rounded" size="10" type="text" name="orsno" id="orsno-input" placeholder="ORS NO" value="" style="color: black; padding-right: 3px; padding-left: 3px; text-align: center;">
                                        </input>    
                                    </td>           
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">
                                        <input required class="container rounded" size="10" type="text" name="uacs" id="uacs-input" placeholder="UACS" value="" style="color: black; padding-right: 3px; padding-left: 3px; text-align: center;">
                                        </input>    
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">
                                        <input disabled class="container rounded" size="10" type="text" id="amount-input" placeholder="Amount" value="" style="color: black; padding-right: 3px; padding-left: 3px; text-align: center;">
                                        </input>    
                                    </td>
                                </form>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><br><br>@endif
            <div class="bg-gray dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" style="color: green">
                    <table>
                        <tbody>
                            <tr>
                                <th colspan="10" class="text-center">RECEIVED VOUCHERS</th>
                            </tr>
                            <tr>
                                <th colspan="10" class="text-center">TO ADD: SEARCH BAR</th>
                            </tr>
                            <tr>
                                <th class="text-center">ACTION</th>
                                <th class="text-center">ORS. NO.</th>
                                <th class="text-center">DV. NO.</th>
                                <th class="text-center">PAYEE</th>
                                <th class="text-center">PARTICULARS</th>
                                <th class="text-center">UACS</th>
                                <th class="text-center">GROSS AMOUNT</th>
                                <th class="text-center">TAX WITHHELD</th>
                                <th class="text-center">NET AMOUNT</th>
                                <th class="text-center">REMARKS</th>
                            </tr>
                             @foreach ($voucher as $data)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                    <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">Action</button>
                                    <ul class="dropdown-menu">
                                        <li><button type="button"
                                                    class="dropdown-item update-button"
                                                    data-id="{{ $data->id }}"
                                                    data-orsno="{{ $data->orsno }}"
                                                    data-uacs="{{ $data->uacs }}"
                                                    data-amount="{{ number_format ($data->amount,2) }}">
                                                Update
                                            </button>
                                                <script>
                                                    document.querySelectorAll('.update-button').forEach(button => {
                                                        button.addEventListener('click', function () {
                                                            document.getElementById('voucher-id').value = this.dataset.id;
                                                            document.getElementById('orsno-input').value = this.dataset.orsno;
                                                            document.getElementById('uacs-input').value = this.dataset.uacs;
                                                            document.getElementById('amount-input').value = this.dataset.amount;
                                                        });
                                                    });
                                                </script>

                                        <li>
                                            <form method="post" action="/delete" class="wbd-form delete-form">@csrf
                                            <input type="hidden" name="id" value="{{$data->id}}">
                                                <button type="button" class="dropdown-item delete-button">
                                                    Delete
                                                </button>
                                            </form>
                                            <script>
                                                document.querySelectorAll('.delete-button').forEach(button => {
                                                    button.addEventListener('click', function(e) {
                                                        const form = this.closest('form');

                                                        Swal.fire({
                                                        title: 'Are you sure?',
                                                        text: "This action cannot be undone.",
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonText: 'Yes, delete it!',
                                                        cancelButtonText: 'Cancel',
                                                        customClass: {
                                                            confirmButton: 'swal-confirm-btn',
                                                            cancelButton: 'swal-cancel-btn'
                                                        }
                                                    }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                form.submit();
                                                            }
                                                        });
                                                    });
                                                });
                                            </script>
                                        </li>
                                    </ul>
                                </td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ ($data->orsno ?? "") }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ ($data->dvno ?? "") }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ ($data->payee ?? "") }}</td>           
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ ($data->particulars ?? "") }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ ($data->uacs ?? "") }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format ($data->amount ?? "",2) }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"></td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;"></td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;"><button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">View Remarks</button><ul class="dropdown-menu"><li>{!! $data->remarks !!}</li></ul></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>{{ $voucher->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
