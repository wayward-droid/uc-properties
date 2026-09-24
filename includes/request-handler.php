<?php
// Included only by contact.php and inspection.php, after bootstrap.php.
$errors = [];
$formType = $formType ?? 'enquiry';
$estates = published_estates();
$formOptions = rows(
    'SELECT o.*,e.name AS estate_name FROM property_options o JOIN estates e ON e.id=o.estate_id WHERE o.published=1 AND e.published=1 ORDER BY e.name,o.size_sqm',
);
$formPrototypes = rows(
    'SELECT c.option_id,p.id,p.name FROM compatibility c JOIN prototypes p ON p.id=c.prototype_id JOIN property_options o ON o.id=c.option_id JOIN estates e ON e.id=o.estate_id WHERE c.approved=1 AND p.published=1 AND o.published=1 AND e.published=1 ORDER BY p.name',
);
$chosenEstate = ctype_digit(input('estate')) ? (int) input('estate') : 0;
$chosenOption = ctype_digit(input('option')) ? (int) input('option') : 0;
$chosenPrototype = ctype_digit(input('prototype')) ? (int) input('prototype') : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (!rate_limit('request', 10, 3600)) {
        $errors[] = 'Please wait before sending another request, or call the team.';
    }
    if (input('website') !== '' || time() - ($_SESSION['form_started'] ?? time()) < 2) {
        $errors[] = 'Please take a moment to review the form and try again.';
    }
    $name = input('name');
    $phoneValue = input('phone');
    $email = input('email');
    $message = input('message');
    $date = input('preferred_date');
    $visit = input('visit_preference');
    if (strlen($name) < 2 || strlen($name) > 120) {
        $errors[] = 'Enter your name (2–120 characters).';
    }
    if (
        !preg_match('/^[+()\d\s-]{7,30}$/', $phoneValue) ||
        strlen(preg_replace('/\D/', '', $phoneValue)) < 7
    ) {
        $errors[] = 'Enter a valid phone number, including country code if needed.';
    }
    if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190)) {
        $errors[] = 'Enter a valid email address or leave it blank.';
    }
    if (strlen($message) > 5000) {
        $errors[] = 'Keep your message under 5,000 characters.';
    }
    if ($formType === 'enquiry' && strlen($message) < 5) {
        $errors[] = 'Tell us a little about your enquiry.';
    }
    if (input('consent') !== '1') {
        $errors[] = 'Please agree to be contacted about this request.';
    }
    if ($chosenEstate && !array_filter($estates, fn($e) => (int) $e['id'] === $chosenEstate)) {
        $errors[] = 'Choose a published estate.';
    }
    if (
        $chosenOption &&
        !array_filter(
            $formOptions,
            fn($o) => (int) $o['id'] === $chosenOption && (int) $o['estate_id'] === $chosenEstate,
        )
    ) {
        $errors[] = 'Choose a plot option belonging to your selected estate.';
    }
    if (
        $chosenPrototype &&
        !array_filter(
            $formPrototypes,
            fn($p) => (int) $p['id'] === $chosenPrototype &&
                (int) $p['option_id'] === $chosenOption,
        )
    ) {
        $errors[] = 'Choose a design approved for your selected plot.';
    }
    if ($formType === 'inspection') {
        if (!$chosenEstate) {
            $errors[] = 'Choose the estate you would like to inspect.';
        }
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        if (
            !$parsed ||
            $parsed->format('Y-m-d') !== $date ||
            $date < date('Y-m-d') ||
            $date > date('Y-m-d', strtotime('+1 year'))
        ) {
            $errors[] = 'Choose a preferred date between today and one year from now.';
        }
        if (!in_array($visit, ['in_person', 'virtual', 'call'], true)) {
            $errors[] = 'Choose a visit preference.';
        }
    }
    if (!$errors) {
        $reference = 'UC-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
        try {
            run(
                'INSERT INTO requests (reference,type,estate_id,option_id,prototype_id,name,phone,email,preferred_date,visit_preference,message,consent_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
                [
                    $reference,
                    $formType,
                    $chosenEstate ?: null,
                    $chosenOption ?: null,
                    $chosenPrototype ?: null,
                    $name,
                    $phoneValue,
                    $email ?: null,
                    $formType === 'inspection' ? $date : null,
                    $formType === 'inspection' ? $visit : null,
                    $message,
                    date('Y-m-d H:i:s'),
                ],
            );
            $requestId = (int) $db->lastInsertId();
        } catch (PDOException $ex) {
            error_log('UC request save: ' . $ex->getMessage());
            $errors[] = 'We could not save your request. Please try again or call us.';
        }
        if (!$errors) {
            // A notification failure never rolls back a saved customer request.
            if (!empty($config['mail']['enabled'])) {
                $sent = false;
                try {
                    $to = $config['mail']['to'];
                    $from = $config['mail']['from'];
                    if (
                        filter_var($to, FILTER_VALIDATE_EMAIL) &&
                        filter_var($from, FILTER_VALIDATE_EMAIL)
                    ) {
                        $subject = 'UC Properties: new ' . $formType . ' ' . $reference;
                        $body =
                            "A new request is saved in your dashboard.\nReference: " .
                            $reference .
                            "\nView securely: " .
                            url('admin/requests.php?id=' . $requestId);
                        $sent = @mail($to, $subject, $body, [
                            'From' => $from,
                            'Content-Type' => 'text/plain; charset=UTF-8',
                        ]);
                    }
                } catch (Throwable $ex) {
                    error_log('UC mail: ' . $ex->getMessage());
                }
                run('UPDATE requests SET notification_status=? WHERE id=?', [
                    $sent ? 'sent' : 'failed',
                    $requestId,
                ]);
            }
            $_SESSION['last_request'] = ['reference' => $reference, 'type' => $formType];
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            $_SESSION['form_started'] = time();
            // Keep the existing staff record, then open the visitor's WhatsApp draft.
            // No WhatsApp message is sent by the website; the visitor taps Send there.
            $lines = [$formType === 'inspection'
                ? 'Hello UC Properties, I would like to request an inspection.'
                : 'Hello UC Properties, I have an enquiry.'];
            $lines[] = 'Reference: ' . $reference;
            $lines[] = 'Name: ' . $name;
            $lines[] = 'Phone: ' . $phoneValue;
            if ($email !== '') $lines[] = 'Email: ' . $email;
            foreach ($estates as $selectedEstate) {
                if ((int) $selectedEstate['id'] === $chosenEstate) {
                    $lines[] = 'Estate: ' . $selectedEstate['name'];
                    $lines[] = url('estate.php?slug=' . $selectedEstate['slug']);
                    break;
                }
            }
            foreach ($formOptions as $selectedOption) {
                if ((int) $selectedOption['id'] === $chosenOption) { $lines[] = 'Plot option: ' . $selectedOption['name']; break; }
            }
            foreach ($formPrototypes as $selectedPrototype) {
                if ((int) $selectedPrototype['id'] === $chosenPrototype) { $lines[] = 'Building design: ' . $selectedPrototype['name']; break; }
            }
            if ($formType === 'inspection') {
                $lines[] = 'Preferred date: ' . $date;
                $lines[] = 'Visit preference: ' . ['in_person' => 'In-person inspection', 'virtual' => 'Ask about a virtual visit', 'call' => 'Call me to discuss'][$visit];
            }
            if ($message !== '') $lines[] = 'Message: ' . $message;
            header('Location: ' . whatsapp(implode("\n", $lines)), true, 303);
            exit();
        }
    }
}
