<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class ChatbotController extends ResourceController
{
    /*
    |--------------------------------------------------------------------------
    | OpenRouter Configuration
    |--------------------------------------------------------------------------
    */

    protected string $apiKey = '';

    protected string $apiUrl =
        'https://openrouter.ai/api/v1/chat/completions';

    /*
    | IMPORTANT:
    | Do NOT call this property "$model".
    | ResourceController already has a $model property.
    */

    protected string $aiModel =
        'openai/gpt-4o-mini';

    /*
    |--------------------------------------------------------------------------
    | RAG Configuration
    |--------------------------------------------------------------------------
    */

    protected int $maxRetrievedDocuments = 3;

    protected int $maxRetries = 2;


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        /*
        | Load OpenRouter API key from .env
        |
        | .env:
        |
        | OPENROUTER_API_KEY=sk-or-v1-xxxxxxxx
        */

        $this->apiKey = trim(
            (string) env('OPENROUTER_API_KEY', '')
        );

        log_message(
            'debug',
            'ChatbotController initialized. OpenRouter API key: ' .
            (
                empty($this->apiKey)
                    ? 'NOT LOADED'
                    : 'LOADED'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CHAT
    |--------------------------------------------------------------------------
    |
    | Main chatbot endpoint.
    |
    | User Question
    |      ↓
    | Knowledge Retrieval
    |      ↓
    | Relevant Barangay Information
    |      ↓
    | OpenRouter
    |      ↓
    | Short Answer
    |
    */

    public function chat()
    {
        $message = trim(
            (string) (
                $this->request->getPost('message')
                ?? ''
            )
        );

        if ($message === '') {

            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'error'   => 'Message is required'
                ]);
        }

        log_message(
            'debug',
            'Chatbot question: ' . $message
        );


        /*
        |--------------------------------------------------------------------------
        | Retrieve Relevant Knowledge
        |--------------------------------------------------------------------------
        */

        $documents = $this->retrieveKnowledge(
            $message
        );

        log_message(
            'debug',
            'RAG documents retrieved: ' .
            count($documents)
        );


        /*
        |--------------------------------------------------------------------------
        | No OpenRouter API Key
        |--------------------------------------------------------------------------
        */

        if (empty($this->apiKey)) {

            log_message(
                'error',
                'OPENROUTER_API_KEY is missing.'
            );

            return $this->response->setJSON([
                'success' => true,

                'response' =>
                    $this->buildFallbackResponse(
                        $message,
                        $documents
                    ),

                'source' =>
                    'local_fallback',

                'ai_available' =>
                    false,

                'retrieved_documents' =>
                    count($documents)
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Build Context
        |--------------------------------------------------------------------------
        */

        $context =
            $this->buildRagContext(
                $documents
            );


        /*
        |--------------------------------------------------------------------------
        | Call OpenRouter
        |--------------------------------------------------------------------------
        */

        try {

            $answer =
                $this->callOpenRouter(
                    $message,
                    $context
                );

            return $this->response->setJSON([
                'success' => true,

                'response' =>
                    $answer,

                'source' =>
                    'openrouter_rag',

                'ai_available' =>
                    true,

                'retrieved_documents' =>
                    count($documents)
            ]);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'OpenRouter error: ' .
                $e->getMessage()
            );


            /*
            |--------------------------------------------------------------------------
            | Local Fallback
            |--------------------------------------------------------------------------
            */

            return $this->response->setJSON([
                'success' => true,

                'response' =>
                    $this->buildFallbackResponse(
                        $message,
                        $documents
                    ),

                'source' =>
                    'local_fallback',

                'ai_available' =>
                    false,

                'retrieved_documents' =>
                    count($documents)
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RETRIEVE KNOWLEDGE
    |--------------------------------------------------------------------------
    */

    private function retrieveKnowledge(
        string $question
    ): array {

        $knowledge =
            $this->getKnowledgeBase();

        if (empty($knowledge)) {
            return [];
        }


        $question =
            $this->normalizeText(
                $question
            );


        /*
        |--------------------------------------------------------------------------
        | Special Document Keywords
        |--------------------------------------------------------------------------
        */

        $documentAliases = [

            'clearance' =>
                [
                    'barangay clearance',
                    'clearance'
                ],

            'residency' =>
                [
                    'certificate of residency',
                    'residency',
                    'resident',
                    'residence'
                ],

            'indigency' =>
                [
                    'certificate of indigency',
                    'indigency',
                    'indigent'
                ],

            'good moral' =>
                [
                    'certificate of good moral',
                    'good moral',
                    'moral character'
                ],

            'job seeker' =>
                [
                    'first time job seeker',
                    'first-time job seeker',
                    'job seeker',
                    'first job'
                ],

            'blotter' =>
                [
                    'blotter',
                    'complaint',
                    'complaints',
                    'incident',
                    'dispute'
                ],

            'census' =>
                [
                    'census',
                    'household',
                    'family member',
                    'household member'
                ]
        ];


        /*
        |--------------------------------------------------------------------------
        | Extract Words
        |--------------------------------------------------------------------------
        */

        $words =
            preg_split(
                '/\s+/',
                $question
            );


        $stopWords = [

            'what',
            'what is',
            'what are',

            'how',
            'how do',
            'how can',

            'where',
            'when',
            'who',
            'why',

            'can',
            'could',
            'would',
            'should',

            'the',
            'a',
            'an',

            'is',
            'are',
            'was',
            'were',

            'do',
            'does',
            'did',

            'i',
            'we',
            'you',
            'he',
            'she',
            'they',

            'my',
            'our',
            'your',

            'for',
            'of',
            'to',
            'in',
            'on',
            'at',

            'and',
            'or',

            'please',
            'tell',
            'me',
            'about',

            'need',
            'want',
            'give',
            'get',

            'apply',
            'application',
            'file',
            'filing',
            'request',
            'requesting',

            'please'
        ];


        $words =
            array_values(
                array_filter(
                    $words,
                    function ($word) use ($stopWords) {

                        return strlen($word) >= 3
                            && !in_array(
                                $word,
                                $stopWords,
                                true
                            );
                    }
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Score Documents
        |--------------------------------------------------------------------------
        */

        $results = [];


        foreach ($knowledge as $document) {

            $title =
                $this->normalizeText(
                    $document['title'] ?? ''
                );

            $category =
                $this->normalizeText(
                    $document['category'] ?? ''
                );

            $keywords =
                $this->normalizeText(
                    $document['keywords'] ?? ''
                );

            $content =
                $this->normalizeText(
                    $document['content'] ?? ''
                );


            $searchText =
                $title . ' ' .
                $category . ' ' .
                $keywords . ' ' .
                $content;


            $score = 0;


            /*
            |--------------------------------------------------------------------------
            | Exact Document Alias Match
            |--------------------------------------------------------------------------
            */

            foreach (
                $documentAliases
                as $aliases
            ) {

                foreach ($aliases as $alias) {

                    if (
                        strpos(
                            $question,
                            $alias
                        ) !== false
                    ) {

                        if (
                            strpos(
                                $title,
                                $alias
                            ) !== false
                        ) {

                            $score += 50;
                        }

                        if (
                            strpos(
                                $keywords,
                                $alias
                            ) !== false
                        ) {

                            $score += 30;
                        }
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Word Matching
            |--------------------------------------------------------------------------
            */

            foreach ($words as $word) {

                if (
                    strpos(
                        $searchText,
                        $word
                    ) !== false
                ) {

                    $score += 2;
                }


                /*
                | Title gets stronger weight
                */

                if (
                    strpos(
                        $title,
                        $word
                    ) !== false
                ) {

                    $score += 8;
                }


                /*
                | Keywords get strong weight
                */

                if (
                    strpos(
                        $keywords,
                        $word
                    ) !== false
                ) {

                    $score += 5;
                }


                /*
                | Category
                */

                if (
                    strpos(
                        $category,
                        $word
                    ) !== false
                ) {

                    $score += 3;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Full Question Match
            |--------------------------------------------------------------------------
            */

            if (
                strlen($question) >= 8 &&
                strpos(
                    $searchText,
                    $question
                ) !== false
            ) {

                $score += 25;
            }


            if ($score > 0) {

                $document['_score'] =
                    $score;

                $results[] =
                    $document;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Sort
        |--------------------------------------------------------------------------
        */

        usort(
            $results,
            function ($a, $b) {

                return
                    ($b['_score'] ?? 0)
                    <=>
                    ($a['_score'] ?? 0);
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Limit
        |--------------------------------------------------------------------------
        */

        return array_slice(
            $results,
            0,
            $this->maxRetrievedDocuments
        );
    }


    /*
    |--------------------------------------------------------------------------
    | BUILD RAG CONTEXT
    |--------------------------------------------------------------------------
    */

    private function buildRagContext(
        array $documents
    ): string {

        if (empty($documents)) {

            return
                "No specific official information was found.";
        }


        $context = '';


        foreach (
            $documents
            as $index => $document
        ) {

            $context .=
                "DOCUMENT " .
                ($index + 1) .
                "\n";

            $context .=
                "TITLE: " .
                ($document['title'] ?? '') .
                "\n";

            $context .=
                "CATEGORY: " .
                ($document['category'] ?? '') .
                "\n";

            $context .=
                "INFORMATION: " .
                ($document['content'] ?? '') .
                "\n\n";
        }


        return trim($context);
    }


    /*
    |--------------------------------------------------------------------------
    | CALL OPENROUTER
    |--------------------------------------------------------------------------
    */

    private function callOpenRouter(
        string $message,
        string $context
    ): string {

        $systemPrompt = <<<PROMPT
You are the Barangay Bacolod Information System Assistant.

Barangay:
Bacolod, Bato, Camarines Sur, Philippines.

Your job is to help residents understand common barangay services and documents.

IMPORTANT RESPONSE STYLE:

- Keep answers SHORT.
- Keep answers SIMPLE.
- Use easy-to-understand English.
- Answer the question directly.
- Do not give long explanations.
- Do not repeat the same information.
- Do not use unnecessary disclaimers.
- Use numbered steps only when the user asks how to apply, get, request, or file something.
- If requirements are known, list them clearly.
- If a fee is known, state it.
- If a processing procedure is known, state it.
- If information is missing, say what is missing.
- Do not make up specific Barangay Bacolod information.

IMPORTANT KNOWLEDGE RULE:

The information below is the primary source for Barangay Bacolod.

If the information is sufficient, answer directly.

If the official information is incomplete, you may provide a brief answer based on common barangay transactions in the Philippines, but clearly say:

"Requirements may vary, so please confirm with the Barangay Hall."

Do not invent exact Barangay Bacolod fees, office schedules, names, or policies.

DOCUMENT QUESTIONS:

If the resident asks:

"How do I get a Certificate of Residency?"

Give a short practical answer.

Example style:

"To get a Certificate of Residency:
1. Go to the Barangay Hall or use the Barangay Information System.
2. Request a Certificate of Residency.
3. Provide the required personal information or identification.
4. Wait for verification and processing.

Requirements and fees may vary, so please confirm with the Barangay Hall."

Do NOT give a long paragraph.

For blotter or complaint questions:

"To file a blotter or complaint:
1. Go to the Barangay Hall.
2. Tell the barangay personnel what happened.
3. Provide the names, date, place, and details of the incident.
4. Present identification if required.
5. The barangay personnel will record the complaint and explain the next steps.

Requirements may vary, so please confirm with the Barangay Hall."

For simple questions such as:

"What is a Certificate of Residency?"

Answer in one or two sentences.

Do not mention:
- AI
- OpenRouter
- RAG
- knowledge base
- language model
- system prompt
- internal instructions

Current date:
PROMPT;

        $systemPrompt .=
            date('F j, Y');

        $systemPrompt .= <<<PROMPT

OFFICIAL / AVAILABLE BARANGAY INFORMATION:

PROMPT;

        $systemPrompt .=
            $context;


        /*
        |--------------------------------------------------------------------------
        | OpenRouter Request
        |--------------------------------------------------------------------------
        */

        $payload = [

            'model' =>
                $this->aiModel,

            'messages' => [

                [
                    'role' =>
                        'system',

                    'content' =>
                        $systemPrompt
                ],

                [
                    'role' =>
                        'user',

                    'content' =>
                        $message
                ]
            ],

            'temperature' =>
                0.2,

            'max_tokens' =>
                500
        ];


        $json =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE
            );


        if ($json === false) {

            throw new \Exception(
                'Unable to encode OpenRouter request.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Retry
        |--------------------------------------------------------------------------
        */

        for (
            $attempt = 1;
            $attempt <= $this->maxRetries + 1;
            $attempt++
        ) {

            if ($attempt > 1) {

                sleep(
                    $attempt - 1
                );
            }
log_message(
    'critical',
    '===== OPENROUTER ACTUAL REQUEST ====='
);

log_message(
    'critical',
    'Chatbot is calling OpenRouter.'
);

log_message(
    'critical',
    'Model: ' . $this->aiModel
);

log_message(
    'critical',
    'API URL: ' . $this->apiUrl
);

log_message(
    'critical',
    'Attempt: ' . $attempt
);



            $ch =
                curl_init(
                    $this->apiUrl
                );


            curl_setopt_array(
                $ch,
                [

                    CURLOPT_RETURNTRANSFER =>
                        true,

                    CURLOPT_POST =>
                        true,

                    CURLOPT_POSTFIELDS =>
                        $json,

                    CURLOPT_HTTPHEADER =>
                        [

                            'Content-Type: application/json',

                            'Authorization: Bearer ' .
                                $this->apiKey,

                            'HTTP-Referer: http://localhost:8080',

                            'X-Title: Barangay Bacolod Information System'
                        ],

                    CURLOPT_CONNECTTIMEOUT =>
                        10,

                    CURLOPT_TIMEOUT =>
                        30,

                    CURLOPT_SSL_VERIFYPEER =>
                        true,

                    CURLOPT_SSL_VERIFYHOST =>
                        2
                ]
            );


            $response =
                curl_exec($ch);


            $curlError =
                curl_error($ch);


            $httpCode =
                curl_getinfo(
                    $ch,
                    CURLINFO_HTTP_CODE
                );


            curl_close($ch);

            log_message(
    'critical',
    '===== OPENROUTER RESPONSE RECEIVED ====='
);

log_message(
    'critical',
    '===== OPENROUTER RESPONSE RECEIVED ====='
);

log_message(
    'critical',
    'HTTP Status: ' . $httpCode
);

log_message(
    'critical',
    'OpenRouter Response: ' . $response
);


            /*
            |--------------------------------------------------------------------------
            | CURL Error
            |--------------------------------------------------------------------------
            */

            if ($curlError) {

                log_message(
                    'error',
                    'OpenRouter cURL error: ' .
                    $curlError
                );

                if (
                    $attempt <=
                    $this->maxRetries
                ) {

                    continue;
                }

                throw new \Exception(
                    'Unable to connect to OpenRouter.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Decode Response
            |--------------------------------------------------------------------------
            */

            $result =
                json_decode(
                    $response,
                    true
                );


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            if ($httpCode >= 200 && $httpCode < 300) {

                if (
                    isset(
                        $result['choices'][0]['message']['content']
                    )
                ) {

                    $answer =
                        $result['choices'][0]['message']['content'];


                    return $this->formatResponse(
                        $answer
                    );
                }


                throw new \Exception(
                    'OpenRouter returned an unexpected response.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Rate Limit
            |--------------------------------------------------------------------------
            */

            if ($httpCode === 429) {

                log_message(
                    'warning',
                    'OpenRouter HTTP 429 on attempt ' .
                    $attempt
                );

                if (
                    $attempt <=
                    $this->maxRetries
                ) {

                    continue;
                }

                throw new \Exception(
                    'OpenRouter rate limit reached.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Server Error
            |--------------------------------------------------------------------------
            */

            if ($httpCode >= 500) {

                log_message(
                    'warning',
                    'OpenRouter server error HTTP ' .
                    $httpCode .
                    ' on attempt ' .
                    $attempt
                );

                if (
                    $attempt <=
                    $this->maxRetries
                ) {

                    continue;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | API Error
            |--------------------------------------------------------------------------
            */

            $errorMessage =
                'OpenRouter API error HTTP ' .
                $httpCode;


            if (
                isset(
                    $result['error']['message']
                )
            ) {

                $errorMessage .=
                    ': ' .
                    $result['error']['message'];
            }


            log_message(
                'error',
                $errorMessage
            );


            throw new \Exception(
                $errorMessage
            );
        }


        throw new \Exception(
            'OpenRouter request failed.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK RESPONSE
    |--------------------------------------------------------------------------
    |
    | Used when OpenRouter is unavailable or API key is missing.
    |
    */

    private function buildFallbackResponse(
        string $question,
        array $documents
    ): string {

        if (empty($documents)) {

            return
                "I don't have enough information to answer that. " .
                "Please contact the Barangay Hall for assistance.";
        }


        $document =
            $documents[0];


        $title =
            trim(
                $document['title'] ?? ''
            );


        /*
        |--------------------------------------------------------------------------
        | Identify Question Type
        |--------------------------------------------------------------------------
        */

        $questionLower =
            strtolower(
                $question
            );


        $isHowQuestion =
            preg_match(
                '/\b(how|apply|get|request|obtain|file|filing|process|procedure)\b/i',
                $questionLower
            );


        /*
        |--------------------------------------------------------------------------
        | Certificate of Residency
        |--------------------------------------------------------------------------
        */

        if (
            stripos(
                $title,
                'Certificate of Residency'
            ) !== false
        ) {

            if ($isHowQuestion) {

                return
                    "To get a Certificate of Residency:\n\n" .
                    "1. Go to the Barangay Hall or use the Barangay Information System.\n" .
                    "2. Request a Certificate of Residency.\n" .
                    "3. Provide your required personal information or identification.\n" .
                    "4. Wait for verification and processing.\n\n" .
                    "Requirements and fees may vary, so please confirm with the Barangay Hall.";
            }


            return
                "A Certificate of Residency is a document that confirms that you are a resident of the barangay.";
        }


        /*
        |--------------------------------------------------------------------------
        | Barangay Clearance
        |--------------------------------------------------------------------------
        */

        if (
            stripos(
                $title,
                'Barangay Clearance'
            ) !== false
        ) {

            if ($isHowQuestion) {

                return
                    "To get a Barangay Clearance:\n\n" .
                    "1. Go to the Barangay Hall or use the Barangay Information System.\n" .
                    "2. Request a Barangay Clearance.\n" .
                    "3. Provide the required personal information or identification.\n" .
                    "4. Wait for verification and processing.\n\n" .
                    "Requirements and fees may vary, so please confirm with the Barangay Hall.";
            }


            return
                "A Barangay Clearance is an official document issued by the barangay for various transactions and purposes.";
        }


        /*
        |--------------------------------------------------------------------------
        | Certificate of Indigency
        |--------------------------------------------------------------------------
        */

        if (
            stripos(
                $title,
                'Certificate of Indigency'
            ) !== false
        ) {

            if ($isHowQuestion) {

                return
                    "To get a Certificate of Indigency:\n\n" .
                    "1. Go to the Barangay Hall or use the Barangay Information System.\n" .
                    "2. Request a Certificate of Indigency.\n" .
                    "3. Provide the required personal information and supporting documents, if requested.\n" .
                    "4. Wait for verification and processing.\n\n" .
                    "Requirements may vary, so please confirm with the Barangay Hall.";
            }


            return
                "A Certificate of Indigency is a barangay document that certifies a person's indigency or financial condition.";
        }


        /*
        |--------------------------------------------------------------------------
        | Certificate of Good Moral
        |--------------------------------------------------------------------------
        */

        if (
            stripos(
                $title,
                'Certificate of Good Moral'
            ) !== false
        ) {

            if ($isHowQuestion) {

                return
                    "To get a Certificate of Good Moral:\n\n" .
                    "1. Go to the Barangay Hall or use the Barangay Information System.\n" .
                    "2. Request the certificate.\n" .
                    "3. Provide the required personal information or identification.\n" .
                    "4. Wait for verification and processing.\n\n" .
                    "Requirements may vary, so please confirm with the Barangay Hall.";
            }


            return
                "A Certificate of Good Moral confirms a person's good moral character or conduct within the barangay.";
        }


        /*
        |--------------------------------------------------------------------------
        | First Time Job Seeker
        |--------------------------------------------------------------------------
        */

        if (
            stripos(
                $title,
                'First Time Job Seeker'
            ) !== false
        ) {

            if ($isHowQuestion) {

                return
                    "To get a First Time Job Seeker Certificate:\n\n" .
                    "1. Go to the Barangay Hall or use the Barangay Information System.\n" .
                    "2. Request the First Time Job Seeker Certificate.\n" .
                    "3. Provide the required information and documents.\n" .
                    "4. Wait for verification and processing.\n\n" .
                    "Eligibility and requirements may vary, so please confirm with the Barangay Hall.";
            }


            return
                "A First Time Job Seeker Certificate is issued to qualified first-time job seekers.";
        }


        /*
        |--------------------------------------------------------------------------
        | Blotter and Complaints
        |--------------------------------------------------------------------------
        */

        if (
            stripos(
                $title,
                'Blotter'
            ) !== false ||
            stripos(
                $title,
                'Complaint'
            ) !== false
        ) {

            return
                "To file a blotter or complaint:\n\n" .
                "1. Go to the Barangay Hall.\n" .
                "2. Explain what happened to the barangay personnel.\n" .
                "3. Provide the details of the incident.\n" .
                "4. Present identification if required.\n" .
                "5. Follow the instructions of the barangay personnel.\n\n" .
                "Requirements and procedures may vary, so please confirm with the Barangay Hall.";
        }


        /*
        |--------------------------------------------------------------------------
        | Generic
        |--------------------------------------------------------------------------
        */

        $content =
            trim(
                $document['content'] ?? ''
            );


        if ($content !== '') {

            return $content;
        }


        return
            "Please visit the Barangay Hall for assistance with this request.";
    }


    /*
    |--------------------------------------------------------------------------
    | KNOWLEDGE BASE
    |--------------------------------------------------------------------------
    */

    private function getKnowledgeBase(): array
    {
        /*
        |--------------------------------------------------------------------------
        | External Knowledge File
        |--------------------------------------------------------------------------
        |
        | app/Knowledge/BarangayDocuments.php
        |
        */

        $knowledgeFile =
            APPPATH .
            'Knowledge/BarangayDocuments.php';


        if (
            is_file(
                $knowledgeFile
            )
        ) {

            $knowledge =
                require $knowledgeFile;


            if (
                is_array(
                    $knowledge
                )
            ) {

                return $knowledge;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Default Knowledge
        |--------------------------------------------------------------------------
        */

        return [

            [
                'title' =>
                    'Barangay Clearance',

                'category' =>
                    'documents',

                'keywords' =>
                    'barangay clearance clearance certificate document apply request obtain get requirements',

                'content' =>
                    'Barangay Clearance is an official barangay document commonly used for various transactions. Residents may request it through the Barangay Information System or Barangay Hall. Exact requirements and fees should be confirmed with Barangay Bacolod.'
            ],


            [
                'title' =>
                    'Certificate of Residency',

                'category' =>
                    'documents',

                'keywords' =>
                    'certificate residency resident residence proof address barangay document apply request obtain get',

                'content' =>
                    'Certificate of Residency is a document that confirms that a person is a resident of the barangay.'
            ],


            [
                'title' =>
                    'Certificate of Indigency',

                'category' =>
                    'documents',

                'keywords' =>
                    'certificate indigency indigent poor financial assistance low income assistance',

                'content' =>
                    'Certificate of Indigency is a barangay document that certifies a person or household as indigent for applicable purposes.'
            ],


            [
                'title' =>
                    'Certificate of Good Moral',

                'category' =>
                    'documents',

                'keywords' =>
                    'certificate good moral moral character good conduct behavior',

                'content' =>
                    'Certificate of Good Moral is a barangay document concerning a person’s good moral character or conduct.'
            ],


            [
                'title' =>
                    'First Time Job Seeker Certificate',

                'category' =>
                    'documents',

                'keywords' =>
                    'first time job seeker jobseeker first job employment certificate work employment',

                'content' =>
                    'The First Time Job Seeker Certificate is issued to qualified first-time job seekers.'
            ],


            [
                'title' =>
                    'Blotter and Complaints',

                'category' =>
                    'barangay services',

                'keywords' =>
                    'blotter complaint complaints incident dispute conflict report filing file',

                'content' =>
                    'Residents may go to the Barangay Hall to report an incident or file a complaint. The barangay personnel will record the complaint and explain the next steps.'
            ],


            [
                'title' =>
                    'Census and Household Information',

                'category' =>
                    'barangay information',

                'keywords' =>
                    'census household family resident population family member household member',

                'content' =>
                    'The Barangay Information System maintains census and household information for residents and households.'
            ],


            [
                'title' =>
                    'Barangay Office Information',

                'category' =>
                    'barangay services',

                'keywords' =>
                    'barangay hall office schedule hours contact location bacolod bato camarines sur',

                'content' =>
                    'Barangay Bacolod is located in Bato, Camarines Sur. Residents may visit or contact the Barangay Hall for official barangay services and information.'
            ]
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE TEXT
    |--------------------------------------------------------------------------
    */

    private function normalizeText(
        string $text
    ): string {

        $text =
            trim(
                $text
            );


        $text =
            preg_replace(
                '/\s+/u',
                ' ',
                $text
            );


        return strtolower(
            $text
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT RESPONSE
    |--------------------------------------------------------------------------
    */

    private function formatResponse(
        string $text
    ): string {

        /*
        |--------------------------------------------------------------------------
        | Remove Markdown
        |--------------------------------------------------------------------------
        */

        $text =
            preg_replace(
                '/```(?:.*?)```/s',
                '',
                $text
            );


        $text =
            preg_replace(
                '/\*\*(.*?)\*\*/s',
                '$1',
                $text
            );


        $text =
            preg_replace(
                '/(?<!\w)\*(.*?)\*(?!\w)/s',
                '$1',
                $text
            );


        $text =
            preg_replace(
                '/^#+\s*/m',
                '',
                $text
            );


        $text =
            preg_replace(
                '/`([^`]+)`/',
                '$1',
                $text
            );


        /*
        |--------------------------------------------------------------------------
        | Remove excessive blank lines
        |--------------------------------------------------------------------------
        */

        $text =
            preg_replace(
                "/\n{3,}/",
                "\n\n",
                $text
            );


        /*
        |--------------------------------------------------------------------------
        | Limit unnecessary whitespace
        |--------------------------------------------------------------------------
        */

        $text =
            trim(
                $text
            );


        return $text;
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE CHAT LOG
    |--------------------------------------------------------------------------
    */

    public function saveLog()
    {
        $residentId =
            $this->request->getPost(
                'resident_id'
            );

        $topic =
            $this->request->getPost(
                'topic'
            );

        $message =
            $this->request->getPost(
                'message'
            );

        $response =
            $this->request->getPost(
                'response'
            );

        $status =
            $this->request->getPost(
                'status'
            ) ?? 'Resolved';


        $db =
            \Config\Database::connect();


        $data = [

            'resident_id' =>
                $residentId,

            'topic' =>
                $topic,

            'message' =>
                $message,

            'response' =>
                $response,

            'status' =>
                $status,

            'created_at' =>
                date(
                    'Y-m-d H:i:s'
                )
        ];


        try {

            $db
                ->table(
                    'chatbot_logs'
                )
                ->insert(
                    $data
                );


            return $this->response
                ->setJSON([
                    'success' =>
                        true,

                    'message' =>
                        'Log saved successfully'
                ]);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Failed to save chatbot log: ' .
                $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' =>
                        false,

                    'error' =>
                        'Failed to save log.'
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | GET CHAT LOGS
    |--------------------------------------------------------------------------
    */

    public function getLogs()
    {
        $db =
            \Config\Database::connect();


        try {

            $logs =
                $db
                    ->table(
                        'chatbot_logs'
                    )
                    ->orderBy(
                        'created_at',
                        'DESC'
                    )
                    ->limit(50)
                    ->get()
                    ->getResultArray();


            return $this->response
                ->setJSON([

                    'success' =>
                        true,

                    'logs' =>
                        $logs
                ]);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Failed to fetch chatbot logs: ' .
                $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' =>
                        false,

                    'error' =>
                        'Failed to fetch logs.'
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | TEST OPENROUTER
    |--------------------------------------------------------------------------
    |
    | Temporary diagnostic endpoint.
    |
    | You can remove this after everything works.
    |
    */

    public function testOpenRouter()
    {
        $key =
            trim(
                (string) env(
                    'OPENROUTER_API_KEY',
                    ''
                )
            );


        return $this->response
            ->setJSON([

                'success' =>
                    true,

                'openrouter_key_loaded' =>
                    !empty($key),

                'key_length' =>
                    strlen($key),

                'key_prefix' =>
                    empty($key)
                        ? ''
                        : substr(
                            $key,
                            0,
                            12
                        ) . '...',

                'model' =>
                    $this->aiModel,

                'api_url' =>
                    $this->apiUrl
            ]);
    }
}