<x-app-layout>
    <style>/*Tables+Input Box+Button*/
        /* Define styles for the table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            /*border: 0px solid #ddd; /* Add border to the entire table */
            }

        /* Style table headers */
        th {
            padding: 10px;
            min-width: unset;
            border: 1px solid #ddd; /* Add border to table header cells */
            }

        tr:hover {
            background-color: yellow;
            }

        .xx {
            background-color: green;
            color: white;
            min-width: 80px;
            height: 40px;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 10px;
            }   
    </style>

    <script type="text/javascript">
            $(".wbd-form").submit(function (e) {

        e.preventDefault();

        swal.fire({
            title: "Are you sure?",
            text: "This will process your request.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: "Yes, Process it!",
            cancelButtonText: "No, Keep it!",
            }).then((result) => {
            if (result.value) {
                $(this).closest(".wbd-form").off("submit").submit();
            }else{
                swal.fire("Cancelled","Your request is safe!",'error')
            }
        });

        });

    </script>

    <script>
                (function () {
            // hold onto the drop down menu
            var dropdownMenu;

            // and when you show it, move it to the body
            $(window).on('show.bs.dropdown', function (e) {
                // dropdown-menu nav
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
    </script>


    <!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
    <div class=" py-12 container-fluid">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" style="color: green">
                   <table>
                        <thead>
                            <tr>
                                <th colspan="5" class="text-center">ENCODED VOUCHERS</th>
                            </tr>
                            <tr>
                                <th class="text-center">FORWARD</th>
                                <th class="text-center">PAYEE</th>
                                <th class="text-center">PARTICULARS</th>
                                <th class="text-center">AMOUNT</th>
                                <th class="text-center">REMARKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <form method="POST" action="submitforward"><button class="btn btn-primary">Submit</button>
                                @CSRF
                                @foreach ($voucher as $data)
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 8px;">
                                            @if (Auth::user()->role === 'budget')
                                            <input type="radio" name="forward_{{ $data->id }}" value="1">For Entry</input>
                                            <input type="hidden" name="data_id[]" value="{{ $data->id }}"></input>
                                            @else
                                            <input type="radio" name="forward_{{ $data->id }}" value="0">For Obligation</input></br>
                                            <input type="radio" name="forward_{{ $data->id }}" value="1">For Entry</input>
                                            <input type="hidden" name="data_id[]" value="{{ $data->id }}"></input>
                                            @endif
                                        </td>
                                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ ($data->payee ?? "") }}</td>           
                                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{{ ($data->particulars ?? "") }}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format ($data->amount ?? "",2) }}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">{!! $data->remarks !!}</td>
                                    </tr>
                                @endforeach
                            </form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
