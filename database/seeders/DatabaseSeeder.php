<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Core Test Accounts
        User::create([
            'username' => 'admin',
            'email' => 'admin@skillcheck.com',
            'password_hash' => Hash::make('password'),
            'role' => 'admin',
            'is_suspended' => false,
        ]);

        $instructor = User::create([
            'username' => 'instructor',
            'email' => 'instructor@skillcheck.com',
            'password_hash' => Hash::make('password'),
            'role' => 'instructor',
            'is_suspended' => false,
        ]);

        $student = User::create([
            'username' => 'student',
            'email' => 'student@skillcheck.com',
            'password_hash' => Hash::make('password'),
            'role' => 'student',
            'is_suspended' => false,
        ]);

        // 2. Create Additional Mock Users
        $instructors = User::factory()->count(3)->create(['role' => 'instructor']);
        $students = User::factory()->count(8)->create(['role' => 'student']);

        $allInstructors = $instructors->concat([$instructor]);
        $allStudents = $students->concat([$student]);

        // 3. Define Realistic English Exam Blueprints with MCQ, Short Answer (QA), and Essay
        $examBlueprints = [
            [
                'title' => 'Introduction to Computer Science',
                'description' => 'A basic assessment covering core programming concepts, algorithms, and fundamental computer science architectures.',
                'questions' => [
                    [
                        'text' => 'Which of the following is an example of a high-level programming language?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => 'Assembly Language', 'correct' => false],
                            ['text' => 'Machine Code', 'correct' => false],
                            ['text' => 'Python', 'correct' => true],
                            ['text' => 'Binary Code', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'What is the time complexity of a binary search algorithm on a sorted list of size N?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => 'O(N)', 'correct' => false],
                            ['text' => 'O(log N)', 'correct' => true],
                            ['text' => 'O(N^2)', 'correct' => false],
                            ['text' => 'O(1)', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'Which data structure follows the Last-In, First-Out (LIFO) principle?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => 'Queue', 'correct' => false],
                            ['text' => 'Stack', 'correct' => true],
                            ['text' => 'Binary Tree', 'correct' => false],
                            ['text' => 'Linked List', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'Which translating program converts source code to machine code all at once before execution?',
                        'type' => 'question_answer',
                        'answers' => ['compiler', 'compilers']
                    ],
                    [
                        'text' => 'Explain recursion in programming and detail the importance of a base case.',
                        'type' => 'essay',
                    ]
                ]
            ],
            [
                'title' => 'General Biology & Genetics Quiz',
                'description' => 'Test your understanding of cellular biology, heredity, DNA replication, and human body ecosystems.',
                'questions' => [
                    [
                        'text' => 'Which organelle is widely known as the powerhouse of the cell?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => 'Cell Nucleus', 'correct' => false],
                            ['text' => 'Ribosome', 'correct' => false],
                            ['text' => 'Mitochondria', 'correct' => true],
                            ['text' => 'Golgi Apparatus', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'What is the primary function of chlorophyll in plant cells?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => 'To store nutrients and water', 'correct' => false],
                            ['text' => 'To absorb sunlight for photosynthesis', 'correct' => true],
                            ['text' => 'To provide rigid cell structure', 'correct' => false],
                            ['text' => 'To assist in cellular respiration', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'How many chromosomes does a normal human somatic cell contain?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => '23 chromosomes', 'correct' => false],
                            ['text' => '46 chromosomes', 'correct' => true],
                            ['text' => '92 chromosomes', 'correct' => false],
                            ['text' => '48 chromosomes', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'What acronym is used for the double-helix molecule carrying genetic instructions in living organisms?',
                        'type' => 'question_answer',
                        'answers' => ['dna', 'deoxyribonucleic acid']
                    ],
                    [
                        'text' => 'Explain the process of natural selection as described by Charles Darwin.',
                        'type' => 'essay',
                    ]
                ]
            ],
            [
                'title' => 'World History: 20th Century Conflicts',
                'description' => 'A review of the socio-political movements, treaties, and turning points of World War I and World War II.',
                'questions' => [
                    [
                        'text' => 'Whose assassination in 1914 directly triggered the outbreak of World War I?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => 'Kaiser Wilhelm II', 'correct' => false],
                            ['text' => 'Archduke Franz Ferdinand', 'correct' => true],
                            ['text' => 'Czar Nicholas II', 'correct' => false],
                            ['text' => 'Winston Churchill', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'In which year did the United States enter World War I?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => '1914', 'correct' => false],
                            ['text' => '1917', 'correct' => true],
                            ['text' => '1918', 'correct' => false],
                            ['text' => '1939', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'What international body was formed after World War I to maintain global peace, though it ultimately failed?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => 'United Nations', 'correct' => false],
                            ['text' => 'League of Nations', 'correct' => true],
                            ['text' => 'NATO Alliance', 'correct' => false],
                            ['text' => 'Warsaw Pact Alliance', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'Which European country was forced to accept sole responsibility for starting WWI under the Treaty of Versailles?',
                        'type' => 'question_answer',
                        'answers' => ['germany']
                    ],
                    [
                        'text' => 'Summarize the primary terms of the Treaty of Versailles and its impact on post-WWI Germany.',
                        'type' => 'essay',
                    ]
                ]
            ],
            [
                'title' => 'English Literature: Hamlet Analysis',
                'description' => 'Critical analysis exam exploring character motivations, themes of tragedy, and Shakespearean soliloquies.',
                'viewable_responses' => false,
                'questions' => [
                    [
                        'text' => 'Who is the ghost that appears in the opening scenes of Hamlet?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => 'Hamlet’s father, the late King of Denmark', 'correct' => true],
                            ['text' => 'Claudius', 'correct' => false],
                            ['text' => 'Polonius', 'correct' => false],
                            ['text' => 'Yorick', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'What is the famous first line of Hamlet’s soliloquy in Act 3, Scene 1?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => '"To be, or not to be, that is the question:"', 'correct' => true],
                            ['text' => '"O, that this too too solid flesh would melt"', 'correct' => false],
                            ['text' => '"Speak the speech, I pray you,"', 'correct' => false],
                            ['text' => '"The play’s the thing wherein I\'ll catch the conscience"', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'How does Ophelia die in the play?',
                        'type' => 'multiple_choice',
                        'options' => [
                            ['text' => 'She is poisoned in the garden.', 'correct' => false],
                            ['text' => 'She drowns in a brook.', 'correct' => true],
                            ['text' => 'She hangs herself in her chambers.', 'correct' => false],
                            ['text' => 'She is killed in battle.', 'correct' => false],
                        ]
                    ],
                    [
                        'text' => 'Which legendary English playwright and poet wrote the tragedy Hamlet?',
                        'type' => 'question_answer',
                        'answers' => ['shakespeare', 'william shakespeare']
                    ],
                    [
                        'text' => 'Describe the theme of revenge in Hamlet and how it affects the protagonist.',
                        'type' => 'essay',
                    ]
                ]
            ]
        ];

        // 4. Populate Exams, Questions, Options, Attempts, and Answers
        foreach ($examBlueprints as $blueprint) {
            $randomInst = $allInstructors->random();

            $exam = Exam::create([
                'instructor_id' => $randomInst->user_id,
                'title' => $blueprint['title'],
                'description' => $blueprint['description'],
                'start_time' => now()->subDays(1),
                'end_time' => now()->addDays(5),
                'duration_s' => 3600, // 1 hour
                'randomize_questions' => rand(0, 1) === 1,
                'viewable_responses' => $blueprint['viewable_responses'] ?? true,
            ]);

            $createdQuestions = [];

            foreach ($blueprint['questions'] as $index => $qData) {
                $question = Question::create([
                    'exam_id' => $exam->exam_id,
                    'order_index' => $index + 1,
                    'question_text' => $qData['text'],
                    'image_url' => null,
                    'type' => $qData['type'],
                    'time_limit_s' => 15, // 15 seconds per question
                    'marks' => 10,
                    'is_locked' => false,
                ]);

                $createdQuestions[] = $question;

                if ($qData['type'] === 'multiple_choice') {
                    foreach ($qData['options'] as $oIdx => $oData) {
                        Option::create([
                            'question_id' => $question->question_id,
                            'order_index' => $oIdx + 1,
                            'option_text' => $oData['text'],
                            'is_correct' => $oData['correct'],
                        ]);
                    }
                } elseif ($qData['type'] === 'question_answer') {
                    // Seed correct answers into Options table for short answer matching
                    foreach ($qData['answers'] as $aIdx => $answerText) {
                        Option::create([
                            'question_id' => $question->question_id,
                            'order_index' => $aIdx + 1,
                            'option_text' => $answerText,
                            'is_correct' => true,
                        ]);
                    }
                }
            }

            // Generate Mock student attempts for this exam
            $numAttempts = (isset($blueprint['viewable_responses']) && !$blueprint['viewable_responses']) ? 1 : 3;
            $allStudents->random($numAttempts)->each(function ($stud) use ($exam, $createdQuestions) {
                $statusStr = ['submitted', 'graded'][rand(0, 1)];

                $attempt = ExamAttempt::create([
                    'exam_id' => $exam->exam_id,
                    'student_id' => $stud->user_id,
                    'start_time' => now()->subMinutes(rand(10, 45)),
                    'end_time' => now(),
                    'status' => $statusStr,
                    'question_order' => collect($createdQuestions)->pluck('question_id')->toArray(),
                ]);

                // Create answers
                foreach ($createdQuestions as $q) {
                    $selectedOpt = null;
                    $textAns = null;
                    $marksAwarded = 0;

                    if ($q->type === 'multiple_choice') {
                        $options = $q->options;
                        $chosenOpt = $options->random();
                        $selectedOpt = $chosenOpt->option_id;
                        
                        if ($attempt->status === 'graded' || $attempt->status === 'submitted') {
                            // MCQs are auto-graded immediately upon submission
                            $marksAwarded = $chosenOpt->is_correct ? $q->marks : 0;
                        }
                    } elseif ($q->type === 'question_answer') {
                        // Fetch correct answer list
                        $correctTexts = $q->options->pluck('option_text')->toArray();
                        
                        // 70% chance to write a correct answer
                        $isCorrect = (rand(1, 10) <= 7);
                        $textAns = $isCorrect ? $correctTexts[0] : 'incorrect short answer';
                        
                        if ($attempt->status === 'graded' || $attempt->status === 'submitted') {
                            // QAs are auto-graded immediately upon submission
                            $marksAwarded = $isCorrect ? $q->marks : 0;
                        }
                    } elseif ($q->type === 'essay') {
                        $textAns = "This is a detailed student response explaining the essay topic in full paragraphs. It covers key theoretical aspects of the question.";
                        
                        if ($attempt->status === 'graded') {
                            // Graded attempts have manual grading already completed
                            $marksAwarded = rand(0, 1) === 1 ? $q->marks : 5.00;
                        } else {
                            // Submitted attempts are waiting for manual instructor check, so marks are null
                            $marksAwarded = null;
                        }
                    }

                    StudentAnswer::create([
                        'attempt_id' => $attempt->attempt_id,
                        'question_id' => $q->question_id,
                        'selected_option' => $selectedOpt,
                        'text_answer' => $textAns,
                        'marks_awarded' => $marksAwarded,
                    ]);
                }
            });
        }
    }
}
