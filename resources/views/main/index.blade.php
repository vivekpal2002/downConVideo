@extends('layout.layout')
@section('page_title', 'downConvideo')
@section('contents')
    <div class="thirteen">
        <h1>downConvideo</h1>
    </div>
    <marquee behavior="scroll" direction="left">We can convert Video Link to GIF</marquee>
    <h2 style="text-align: center">For Download Video</h2>
    <div style="border:2px solid #222">
        <form onsubmit="return false;">
            @csrf
            <h3>Paste the Link Here...</h3>
            <input type="text" class="link_video cls_input" name="link_video" placeholder="https://example.com/video.mp4">
            <button type="button" class="cls_video_submit third">Submit</button>
        </form>
    </div>

    <h2 style="text-align: center">For Download GIF</h2>
    <div style="border:2px solid #222">
        <form onsubmit="return false;" enctype="multipart/form-data">
            @csrf
            <h3>Paste Video Link:</h3>
            <input type="text" name="link_video_1" class="link_video_1 cls_input" placeholder="https://example.com/video.mp4">
            <p>OR Upload Video:</p>
            <input type="file" class="upload_video" accept="video/mp4">
            <button type="submit" class="cls_gif_submit third">Convert to GIF</button>
        </form>
    </div>

    <div class="video_player"> </div>


    <script>
        $('.cls_video_submit').click(function() {
            $.ajax({
                type: 'POST',
                url: 'download',
                data: {
                    '_token': '{{ csrf_token() }}',
                    link_video: $('.link_video').val()
                },
                success: function(response) {
                    $('.video_player').html(response)
                }
            });
        })
        $('.cls_gif_submit').click(function() {
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('link_video_1', $('.link_video_1').val());

            // If user uploads a file
            if ($('.upload_video')[0].files.length > 0) {
                formData.append('upload_video', $('.upload_video')[0].files[0]);
            }

            $.ajax({
                type: 'POST',
                url: '/convert-to-gif',
                data: formData,
                processData: false, // prevent jQuery from processing
                contentType: false, // prevent jQuery from setting content type
                success: function(response) {
                    $('.video_player').html(response);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert("Something went wrong!");
                }
            });
        });
    </script>
@endsection
