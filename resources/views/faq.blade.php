@extends('layouts.app')

@section('content')
<a href="{{ url()->previous() }}" class="go-back-button">Go Back</a>
    <div class="help-card">
        <h1 class = "help-title">Help / Frequently Asked Questions</h1>
        <section id="questions" class="faq-questions">
            <article class="question">
                <a data-toggle="collapse" href="#answer1" role="button">
                    <h2>+ How to use the application?</h2>
                </a>
                <div class="collapse" id="answer1">
                    <p>To use Lumea, simply create an account, log in, and start exploring the available challenges. You can join existing challenges or create your own. Share your progress, rate other participants, and track your performance over time.</p>
                </div>
            </article>        
            
            <article class="question">
                <a data-toggle="collapse" href="#answer2" role="button">
                    <h2>+ I forgot my password, what can I do?</h2>
                </a>
                <div class="collapse" id="answer2">
                    <p>You may click on the button labeled 'Recover my password' on the login page, and follow the instructions there. If you need further help please don't hesitate to contact us.</p>
                </div>
            </article>        

            <article class="question">
                <a data-toggle="collapse" href="#answer3" role="button">
                    <h2>+ How can I delete my account?</h2>
                </a>
                <div class="collapse" id="answer3">
                    <p>In the sidebar on your profile page, you will see a button labeled 'Delete account'. Clicking the button will ask you for a confirmation, if you accept, your account will be deleted. Be careful, this action cannot be reversed!</p>
                </div>
            </article>

            <article class="question">
                <a data-toggle="collapse" href="#answer4" role="button">
                    <h2>+ Why did my post disappear?</h2>
                </a>
                <div class="collapse" id="answer4">
                    <p>You may have posted content that breached our Terms of Service, in which case it may have been deleted by an administrator. If you think this action was not justified, please contact a staff member.</p>
                </div>
            </article>

            <article class="question">
                <a data-toggle="collapse" href="#answer5" role="button">
                    <h2>+ Why was my account blocked?</h2>
                </a>
                <div class="collapse" id="answer5">
                    <p>Some of your actions were deemed as breaching our Terms of Service, in which case you may have been blocked by an administrator. If you think this action was not justified, please contact a staff member.</p>
                </div>
            </article> 
        </section>
    </div>

    <section class="ask-question">
        <h2>Ask a Question</h2>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if (Auth::check())
            <form id="question-form" method="POST" action="{{ route('faq.sendQuestion') }}">
                @csrf
                <textarea name="question" id="question" rows="4" placeholder="Write your question here..." required></textarea>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        @else
            <p>You need to log in first to send a question to the admins.</p>
        @endif
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection