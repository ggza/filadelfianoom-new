{{-- TinyMCE 7.9.3 szövegszerkesztő, helyben tárolva (public/vendor/tinymce), GPL licenc --}}
@push('scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script>
tinymce.init({
  selector: 'textarea.rich',
  license_key: 'gpl',
  language: 'hu_HU',
  height: 460,
  menubar: false,
  branding: false,
  promotion: false,
  plugins: 'lists link code autoresize',
  toolbar: 'undo redo | blocks | bold italic | bullist numlist | link | removeformat | code',
  block_formats: 'Bekezdés=p; Alcím=h3',
  extended_valid_elements: 'span[class|aria-hidden],form[class|action|method|target],input[type|name|value|src|alt|border]',
  content_css: '{{ asset('assets/css/site.css') }}',
  content_style: 'body{padding:12px;background:#f7f4ea}',
  convert_urls: false,
});
</script>
@endpush
