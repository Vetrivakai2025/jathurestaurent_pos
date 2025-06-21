@extends('layouts.master')
@section('main-content')

<div class="breadcrumb">
    <h1>Send SMS</h1>
</div>
<div class="row" id="smsApp">
    <div class="col-md-12">
        <div class="card">
            <form @submit.prevent="sendSms">
                <div class="card-body">

                    <div class="form-group">
                        <label>Message (will be sent to all clients)</label>
                        <textarea v-model="form.message" class="form-control" rows="3" placeholder="Enter message to send to all clients"></textarea>
                        <small>@{{ form.message.length }}/1000 characters</small>
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm"></span>
                        Send SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page-js')
<script>
new Vue({
    el: '#smsApp',
    data: {
        form: {
            recipientType: 'all', // 🔒 Fixed to 'all'
            message: ''
        },
        loading: false
    },
    methods: {
        sendSms() {
            this.loading = true;

            const payload = {
                message: this.form.message,
                client_id: 'all' // 🔒 Send to all clients
            };

            axios.post('/sms/send', payload)
                .then(response => {
                    toastr.success(response.data.message);
                    this.form.message = '';
                })
                .catch(error => {
                    const errorMsg = error.response?.data?.message || error.message;
                    toastr.error('Error: ' + errorMsg);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
});
</script>
@endsection
