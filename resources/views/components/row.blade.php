@push('scripts')
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}

<script>
    $(document).ready(function() {
        const messages = [
            "اللهم صلي على الحبيب محمد.",
            "اللهم اجعل يومنا هذا خيرًا لنا في الدين والدنيا.",
            "اللهم ارزقنا التوفيق والقبول.",
            "يا رب اجعل القرآن ربيع قلوبنا.",
            "اللهم اغفر لنا ولوالدينا ولجميع المسلمين.",
            "اللهم اشف مرضانا ومرضى المسلمين.",
            "اللهم ارحم موتانا وموتى المسلمين.",
            "اللهم اجعلنا من الذاكرين الشاكرين."
        ];

        let index = 0;
        const interval = 30000; // 30 ثانية

        function showToast() {
            toastr.success(messages[index]);
            index = (index + 1) % messages.length;
        }

        if (!sessionStorage.getItem('toast_shown')) {
            // أول مرة فقط في الجلسة
            showToast();

            // بعد 30 ثانية يبدأ التكرار
            setTimeout(function() {
                showToast();
                setInterval(showToast, interval);
            }, interval);

            // علمنا أن التوست ظهر في هذه الجلسة
            sessionStorage.setItem('toast_shown', 'true');
        }
    });
    </script>

@endpush
