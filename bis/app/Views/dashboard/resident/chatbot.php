<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chatbot Support - Bacolod BIS</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="/style.css">
</head>

<body class="db-body">

    <?php
    $role = 'resident';
    $active = 'chatbot';
    $pageTitle = 'Chatbot Support';

    include(APPPATH . 'Views/dashboard/sidebar.php');
    ?>

    <div class="db-main">

        <?php include(APPPATH . 'Views/dashboard/topbar.php'); ?>

        <div class="db-content">

            <!-- =========================================================
                 CHATBOT STATS
            ========================================================== -->

            <div class="db-stats" style="margin-bottom:24px;">

                <div class="db-stat-card">
                    <div
                        class="db-stat-icon"
                        style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-robot"></i>
                    </div>

                    <div>
                        <span class="db-stat-num">BIS Bot</span>
                        <span class="db-stat-label">AI Assistant</span>
                    </div>
                </div>


                <div class="db-stat-card">
                    <div
                        class="db-stat-icon"
                        style="background:rgba(22,199,154,0.15);color:#16c79a;">
                        <i class="fas fa-circle"></i>
                    </div>

                    <div>
                        <span class="db-stat-num">Online</span>
                        <span class="db-stat-label">Available 24/7</span>
                    </div>
                </div>


                <div class="db-stat-card">
                    <div
                        class="db-stat-icon"
                        style="background:rgba(255,193,7,0.15);color:#ffc107;">
                        <i class="fas fa-comments"></i>
                    </div>

                    <div>
                        <span class="db-stat-num">7</span>
                        <span class="db-stat-label">Quick Topics</span>
                    </div>
                </div>


                <div class="db-stat-card">
                    <div
                        class="db-stat-icon"
                        style="background:rgba(91,111,214,0.15);color:#5b6fd6;">
                        <i class="fas fa-clock"></i>
                    </div>

                    <div>
                        <span class="db-stat-num">&lt;1s</span>
                        <span class="db-stat-label">Response Time</span>
                    </div>
                </div>

            </div>


            <!-- =========================================================
                 CHATBOT INTRO
            ========================================================== -->

            <div class="chatbot-intro-card">

                <div class="chatbot-intro-left">

                    <div class="chatbot-intro-avatar">
                        <i class="fas fa-robot"></i>
                    </div>

                    <div>

                        <h3>BIS Chatbot Assistant</h3>

                        <p>
                            Get instant answers about barangay services,
                            clearances, census statistics, account concerns,
                            and other BIS services.
                        </p>

                    </div>

                </div>


                <button
                    class="chatbot-open-btn"
                    onclick="toggleChat()">

                    <i class="fas fa-comment-dots"></i>
                    Start Chat

                </button>

            </div>


            <!-- =========================================================
                 RESIDENT ACCESS INFORMATION
            ========================================================== -->

            <div
                style="
                    margin-top:20px;
                    margin-bottom:24px;
                    padding:16px 20px;
                    border-radius:12px;
                    background:rgba(91,111,214,0.07);
                    border:1px solid rgba(91,111,214,0.15);
                ">

                <div
                    style="
                        display:flex;
                        align-items:flex-start;
                        gap:12px;
                    ">

                    <div
                        style="
                            width:36px;
                            height:36px;
                            min-width:36px;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            background:rgba(91,111,214,0.15);
                            color:#5b6fd6;
                        ">

                        <i class="fas fa-info-circle"></i>

                    </div>

                    <div>

                        <strong
                            style="
                                display:block;
                                margin-bottom:4px;
                            ">
                            Resident Chatbot Access
                        </strong>

                        <p
                            style="
                                margin:0;
                                line-height:1.6;
                                color:#666;
                                font-size:14px;
                            ">

                            You can ask about barangay services and
                            <strong>aggregate census statistics</strong>,
                            such as population, gender, age groups,
                            civil status, employment, and years of residency.
                            Individual personal information of other residents
                            is not available through the chatbot.

                        </p>

                    </div>

                </div>

            </div>


            <!-- =========================================================
                 QUICK TOPICS
            ========================================================== -->

            <h3 class="db-section-title">
                Quick Topics
            </h3>


            <div class="chatbot-topics-grid">


                <!-- =====================================================
                     BARANGAY CLEARANCE
                ====================================================== -->

                <div
                    class="chatbot-topic-card"
                    onclick="sendQuick('How do I apply for barangay clearance?');toggleChat();">

                    <div class="chatbot-topic-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>

                    <h4>Barangay Clearance</h4>

                    <p>
                        Requirements, fees, and processing time
                    </p>

                </div>


                <!-- =====================================================
                     CERTIFICATE OF RESIDENCY
                ====================================================== -->

                <div
                    class="chatbot-topic-card"
                    onclick="sendQuick('What are the requirements for certificate of residency?');toggleChat();">

                    <div class="chatbot-topic-icon">
                        <i class="fas fa-home"></i>
                    </div>

                    <h4>Certificate of Residency</h4>

                    <p>
                        How to get proof of residence
                    </p>

                </div>


                <!-- =====================================================
                     CENSUS STATISTICS
                ====================================================== -->

                <div
                    class="chatbot-topic-card"
                    onclick="sendQuick('What is the census summary?');toggleChat();">

                    <div class="chatbot-topic-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>

                    <h4>Census Statistics</h4>

                    <p>
                        Population and aggregate census information
                    </p>

                </div>


                <!-- =====================================================
                     CENSUS UPDATE
                ====================================================== -->

                <div
                    class="chatbot-topic-card"
                    onclick="sendQuick('How do I update my census information?');toggleChat();">

                    <div class="chatbot-topic-icon">
                        <i class="fas fa-users"></i>
                    </div>

                    <h4>Census Update</h4>

                    <p>
                        Update your household information
                    </p>

                </div>


                <!-- =====================================================
                     OFFICE HOURS
                ====================================================== -->

                <div
                    class="chatbot-topic-card"
                    onclick="sendQuick('What are the barangay office hours?');toggleChat();">

                    <div class="chatbot-topic-icon">
                        <i class="fas fa-clock"></i>
                    </div>

                    <h4>Office Hours</h4>

                    <p>
                        When is the barangay hall open?
                    </p>

                </div>


                <!-- =====================================================
                     FEES
                ====================================================== -->

                <div
                    class="chatbot-topic-card"
                    onclick="sendQuick('What is the fee for barangay clearance?');toggleChat();">

                    <div class="chatbot-topic-icon">
                        <i class="fas fa-coins"></i>
                    </div>

                    <h4>Fees &amp; Payments</h4>

                    <p>
                        Cost of documents and services
                    </p>

                </div>


                <!-- =====================================================
                     FILE A COMPLAINT
                ====================================================== -->

                <div
                    class="chatbot-topic-card"
                    onclick="sendQuick('How do I file a complaint?');toggleChat();">

                    <div class="chatbot-topic-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>

                    <h4>File a Complaint</h4>

                    <p>
                        Report issues to the barangay
                    </p>

                </div>

            </div>


            <!-- =========================================================
                 OPTIONAL CENSUS EXAMPLES
            ========================================================== -->

            <div style="margin-top:28px;">

                <h3 class="db-section-title">
                    You Can Also Ask
                </h3>

                <div
                    style="
                        display:flex;
                        flex-wrap:wrap;
                        gap:10px;
                    ">

                    <button
                        type="button"
                        onclick="sendQuick('How many residents are there in the barangay?');toggleChat();"
                        style="
                            border:1px solid #e1e1e1;
                            background:#fff;
                            border-radius:20px;
                            padding:9px 15px;
                            cursor:pointer;
                            font-family:inherit;
                            font-size:13px;
                        ">

                        <i class="fas fa-users"></i>
                        How many residents are there?

                    </button>


                    <button
                        type="button"
                        onclick="sendQuick('How many are male and female?');toggleChat();"
                        style="
                            border:1px solid #e1e1e1;
                            background:#fff;
                            border-radius:20px;
                            padding:9px 15px;
                            cursor:pointer;
                            font-family:inherit;
                            font-size:13px;
                        ">

                        <i class="fas fa-venus-mars"></i>
                        Male and female count

                    </button>


                    <button
                        type="button"
                        onclick="sendQuick('How many residents are single?');toggleChat();"
                        style="
                            border:1px solid #e1e1e1;
                            background:#fff;
                            border-radius:20px;
                            padding:9px 15px;
                            cursor:pointer;
                            font-family:inherit;
                            font-size:13px;
                        ">

                        <i class="fas fa-user"></i>
                        How many are single?

                    </button>


                    <button
                        type="button"
                        onclick="sendQuick('What is the summary of years of residency?');toggleChat();"
                        style="
                            border:1px solid #e1e1e1;
                            background:#fff;
                            border-radius:20px;
                            padding:9px 15px;
                            cursor:pointer;
                            font-family:inherit;
                            font-size:13px;
                        ">

                        <i class="fas fa-calendar-alt"></i>
                        Years of residency

                    </button>


                    <button
                        type="button"
                        onclick="sendQuick('What is the age distribution of the residents?');toggleChat();"
                        style="
                            border:1px solid #e1e1e1;
                            background:#fff;
                            border-radius:20px;
                            padding:9px 15px;
                            cursor:pointer;
                            font-family:inherit;
                            font-size:13px;
                        ">

                        <i class="fas fa-birthday-cake"></i>
                        Age distribution

                    </button>


                    <button
                        type="button"
                        onclick="sendQuick('What is the employment summary?');toggleChat();"
                        style="
                            border:1px solid #e1e1e1;
                            background:#fff;
                            border-radius:20px;
                            padding:9px 15px;
                            cursor:pointer;
                            font-family:inherit;
                            font-size:13px;
                        ">

                        <i class="fas fa-briefcase"></i>
                        Employment summary

                    </button>

                </div>

            </div>

        </div>
    </div>


    <!-- =============================================================
         SIDEBAR MOBILE CLOSE
    ============================================================= -->

    <script>
        document
            .querySelectorAll('.db-nav-item')
            .forEach(function (item) {

                item.addEventListener('click', function () {

                    const sidebar =
                        document.getElementById('sidebar');

                    if (sidebar) {
                        sidebar.classList.remove('open');
                    }

                });

            });
    </script>

</body>

</html>