<!DOCTYPE html>
<html>
<head>
    <title>FAQ Answered</title>
</head>
<body>
    <h1>FAQ Answered</h1>
    <p>Question:</p>
    <blockquote>{{ $question }}</blockquote>
    <p>Answer:</p>
    <blockquote>{{ $answer }}</blockquote>
    @if ($isEdited)
        <p>(resposta corrigida)</p>
    @endif
</body>
</html>