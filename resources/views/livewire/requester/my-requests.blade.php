<div>
    <h2 class="mb-3">My Requests</h2>

    <div wire:ignore>
        <table class="table table-hover w-100" id="my-requests-table">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    @include('livewire.requester.modal.request-detail-modal')

    <script>
        function initMyRequestsTable() {
            if ($.fn.DataTable.isDataTable('#my-requests-table')) {
                $('#my-requests-table').DataTable().destroy();
            }

            $('#my-requests-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("requester.my-requests.fetch") }}',
                columns: [
                    { data: 'id', name: 'id', render: (data) => '#' + data },
                    { data: 'items', name: 'items', orderable: false, searchable: false },
                    { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                    { data: 'submitted', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            });
        }

        document.addEventListener('livewire:navigated', initMyRequestsTable);
        document.addEventListener('DOMContentLoaded', initMyRequestsTable);

        function viewRequest(id) {
            Livewire.dispatch('viewRequest', { requestId: id });
        }

        function cancelRequestConfirm(id) {
            Swal.fire({
                title: 'Cancel this request?',
                text: 'This cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'No',
                confirmButtonColor: '#dc3545',
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('cancelRequest', { requestId: id });
                }
            });
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('reloadTable', () => {
                $('#my-requests-table').DataTable().ajax.reload();
            });
        });
    </script>
</div>