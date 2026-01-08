<?php
$publisher_search = "";
$title_search     = "";
$insert_success   = false;

require_once __DIR__ . "/bc_add_pub_title.php";
?>


<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Publisher & Title</title>
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

        <style>
            :root {
                --light-bg: #dbdbdb; 
                --dark-bg: #20252d;
                --input-bg: #f2f2f2;
                --btn-add-color: #8f8989;    
                --btn-return-color: #3c4862; 
                --success-bg: #20252d;
                --header-font: 'Cinzel', serif;
                --body-font: 'Montserrat', sans-serif;
            }

            * { box-sizing: border-box; }

            html, body {
                margin: 0; padding: 0; min-height: 100vh;
                font-family: var(--body-font);
                background-color: var(--light-bg);
                display: flex; flex-direction: column;
            }

            /* HEADER */
            .top-section {
                background-color: var(--dark-bg); height: 250px;
                display: flex; flex-direction: column; justify-content: center; align-items: center; position: relative;
            }
            .logo-top { position: absolute; top: 30px; left: 40px; width: 180px; }
            .page-title-img { width: 500px; max-width: 85%; height: auto; margin-top: 30px; }
            .instruction-text { color: white; font-size: 14px; margin-top: 15px; opacity: 0.9; }

            /* MAIN SELECTION AREA */
            .selection-container {
                flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 25px; padding: 50px;
            }
            
            .big-btn {
                padding: 20px 0; width: 350px;
                border-radius: 50px; border: none;
                font-family: var(--header-font); font-weight: 700; font-size: 18px;
                color: white; cursor: pointer; text-transform: uppercase;
                box-shadow: 0 6px 15px rgba(0,0,0,0.25); transition: transform 0.2s;
                text-decoration: none; display: flex; justify-content: center; align-items: center;
            }
            .big-btn:hover { transform: scale(1.03); }
            .btn-add { background-color: var(--btn-add-color); }
            .btn-return { background-color: var(--btn-return-color); }

            /* MODAL FORMS */
            .modal-overlay {
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.6); backdrop-filter: blur(5px);
                display: none; justify-content: center; align-items: center; z-index: 1000;
            }

            .form-card {
                background-color: white; width: 900px; max-width: 95vw;
                padding: 40px; border-radius: 20px; position: relative;
                max-height: 90vh; overflow-y: auto;
                box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            }

            .close-icon {
                position: absolute; top: 20px; right: 20px; font-size: 24px; cursor: pointer; color: #555;
            }

            .form-title {
                font-family: var(--header-font); font-size: 24px; color: var(--dark-bg);
                text-align: center; margin-bottom: 30px; border-bottom: 1px solid #ccc; padding-bottom: 10px;
            }

            /* GRID FORM */
            .form-grid {
                display: grid; grid-template-columns: 1fr 1fr; gap: 25px; text-align: left;
            }
            .full-width { grid-column: 1 / -1; }

            .input-group { display: flex; flex-direction: column; }
            
            .text-label {
                font-family: var(--header-font); font-size: 16px; font-weight: 700; color: #444;
                margin-bottom: 8px; margin-left: 10px; text-transform: uppercase;
            }
            
            .table-input {
                width: 100%; padding: 15px 20px; border-radius: 50px; border: 1px solid #ccc;
                background-color: var(--input-bg); font-family: var(--body-font); font-size: 15px;
            }

            /* Styling for dropdowns */
            select.table-input {
                appearance: none;
                cursor: pointer;
                background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 1rem center;
                background-size: 1em;
            }

            .submit-btn-container {
                margin-top: 40px; display: flex; justify-content: center;
            }
            .submit-btn {
                color: white; border: none; background-color: var(--btn-add-color);
                padding: 15px 60px; border-radius: 30px; font-weight: 700; cursor: pointer;
                font-family: var(--body-font); text-transform: uppercase;
                box-shadow: 0 4px 10px rgba(0,0,0,0.2); font-size: 16px;
            }
            .submit-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }

            /* SUCCESS MODAL */
            .success-box {
                background-color: var(--success-bg); width: 500px; padding: 50px; border-radius: 15px; text-align: center; color: white; display: flex; flex-direction: column; align-items: center; gap: 25px; box-shadow: 0 20px 60px rgba(0,0,0,0.8);
            }
            .success-text { font-family: var(--header-font); font-size: 28px; line-height: 1.3; }
            .btn-done {
                background-color: #f0f0f0; color: #20252d; border: none; padding: 12px 50px; border-radius: 30px; font-weight: 700; cursor: pointer; font-family: var(--body-font);
            }

            .hidden { display: none !important; }
        </style>
    </head>

    <body>

    <div class="modal-overlay <?php echo $show_modal ? '' : 'hidden'; ?>" id="successModal" style="<?php echo $show_modal ? 'display:flex;' : ''; ?>">
        <div class="success-box">
            <h2 class="success-text"><?php echo $success_message; ?></h2>
            <button class="btn-done" onclick="window.location.href='add_publisher_title.php'">DONE</button>
        </div>
    </div>

    <div class="top-section">
        <img src="assets/text/logo.png" class="logo-top" alt="Logo">
        <img src="assets/text/title-publishers-titles.png" class="page-title-img" alt="Publishers & Titles">
        <p class="instruction-text">Complete the Publisher information first, then proceed to Title information.</p>
    </div>

    <div class="selection-container">
        <button class="big-btn btn-add" onclick="openModal('pubModal')">ADD NEW INPUT</button>
        <a href="add_database.php" class="big-btn btn-return">Return to Main Menu</a>
    </div>

    <div class="modal-overlay hidden" id="pubModal">
        <div class="form-card">
            <span class="close-icon" onclick="closeModal('pubModal')">&times;</span>
            <div class="form-title">STEP 1: ADD NEW PUBLISHER</div>
            
            <form id="pubForm" onsubmit="handlePublisherSubmit(event)">
                <div class="form-grid">
                    <div class="input-group full-width">
                        <label class="text-label">Publisher Name</label>
                        <input type="text" id="pub_name" class="table-input" required>
                    </div>
                    <div class="input-group">
                        <label class="text-label">City</label>
                        <input type="text" id="city" class="table-input" required>
                    </div>
                    <div class="input-group">
                        <label class="text-label">State</label>
                        <input type="text" id="state" class="table-input">
                    </div>

                    <div class="input-group full-width">
                        <label class="text-label">Country</label>
                        <select id="country" class="table-input" required>
                            <option value="" disabled selected>Select Country</option>
                            
                            <optgroup label="A">
                                <option value="Afghanistan">Afghanistan</option>
                                <option value="Albania">Albania</option>
                                <option value="Algeria">Algeria</option>
                                <option value="Andorra">Andorra</option>
                                <option value="Angola">Angola</option>
                                <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Australia">Australia</option>
                                <option value="Austria">Austria</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                            </optgroup>
                            
                            <optgroup label="B">
                                <option value="Bahamas">Bahamas</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Barbados">Barbados</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Belgium">Belgium</option>
                                <option value="Belize">Belize</option>
                                <option value="Benin">Benin</option>
                                <option value="Bhutan">Bhutan</option>
                                <option value="Bolivia">Bolivia</option>
                                <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Brazil">Brazil</option>
                                <option value="Brunei">Brunei</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Burundi">Burundi</option>
                            </optgroup>

                            <optgroup label="C">
                                <option value="Cabo Verde">Cabo Verde</option>
                                <option value="Cambodia">Cambodia</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Canada">Canada</option>
                                <option value="Central African Republic">Central African Republic</option>
                                <option value="Chad">Chad</option>
                                <option value="Chile">Chile</option>
                                <option value="China">China</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Comoros">Comoros</option>
                                <option value="Congo (Congo-Brazzaville)">Congo (Congo-Brazzaville)</option>
                                <option value="Costa Rica">Costa Rica</option>
                                <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                                <option value="Croatia">Croatia</option>
                                <option value="Cuba">Cuba</option>
                                <option value="Cyprus">Cyprus</option>
                                <option value="Czechia (Czech Republic)">Czechia (Czech Republic)</option>
                            </optgroup>

                            <optgroup label="D">
                                <option value="Democratic Republic of the Congo">Democratic Republic of the Congo</option>
                                <option value="Denmark">Denmark</option>
                                <option value="Djibouti">Djibouti</option>
                                <option value="Dominica">Dominica</option>
                                <option value="Dominican Republic">Dominican Republic</option>
                            </optgroup>

                            <optgroup label="E">
                                <option value="Ecuador">Ecuador</option>
                                <option value="Egypt">Egypt</option>
                                <option value="El Salvador">El Salvador</option>
                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                <option value="Eritrea">Eritrea</option>
                                <option value="Estonia">Estonia</option>
                                <option value="Eswatini">Eswatini</option>
                                <option value="Ethiopia">Ethiopia</option>
                            </optgroup>

                            <optgroup label="F">
                                <option value="Fiji">Fiji</option>
                                <option value="Finland">Finland</option>
                                <option value="France">France</option>
                            </optgroup>

                            <optgroup label="G">
                                <option value="Gabon">Gabon</option>
                                <option value="Gambia">Gambia</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Germany">Germany</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Greece">Greece</option>
                                <option value="Grenada">Grenada</option>
                                <option value="Guatemala">Guatemala</option>
                                <option value="Guinea">Guinea</option>
                                <option value="Guinea-Bissau">Guinea-Bissau</option>
                                <option value="Guyana">Guyana</option>
                            </optgroup>

                            <optgroup label="H">
                                <option value="Haiti">Haiti</option>
                                <option value="Holy See (Vatican City)">Holy See (Vatican City)</option>
                                <option value="Honduras">Honduras</option>
                                <option value="Hungary">Hungary</option>
                            </optgroup>

                            <optgroup label="I">
                                <option value="Iceland">Iceland</option>
                                <option value="India">India</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="Iran">Iran</option>
                                <option value="Iraq">Iraq</option>
                                <option value="Ireland">Ireland</option>
                                <option value="Israel">Israel</option>
                                <option value="Italy">Italy</option>
                            </optgroup>

                            <optgroup label="J">
                                <option value="Jamaica">Jamaica</option>
                                <option value="Japan">Japan</option>
                                <option value="Jordan">Jordan</option>
                            </optgroup>

                            <optgroup label="K">
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Kiribati">Kiribati</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                            </optgroup>

                            <optgroup label="L">
                                <option value="Laos">Laos</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Lesotho">Lesotho</option>
                                <option value="Liberia">Liberia</option>
                                <option value="Libya">Libya</option>
                                <option value="Liechtenstein">Liechtenstein</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Luxembourg">Luxembourg</option>
                            </optgroup>

                            <optgroup label="M">
                                <option value="Madagascar">Madagascar</option>
                                <option value="Malawi">Malawi</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Maldives">Maldives</option>
                                <option value="Mali">Mali</option>
                                <option value="Malta">Malta</option>
                                <option value="Marshall Islands">Marshall Islands</option>
                                <option value="Mauritania">Mauritania</option>
                                <option value="Mauritius">Mauritius</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Micronesia">Micronesia</option>
                                <option value="Moldova">Moldova</option>
                                <option value="Monaco">Monaco</option>
                                <option value="Mongolia">Mongolia</option>
                                <option value="Montenegro">Montenegro</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Myanmar (formerly Burma)">Myanmar (formerly Burma)</option>
                            </optgroup>

                            <optgroup label="N">
                                <option value="Namibia">Namibia</option>
                                <option value="Nauru">Nauru</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Netherlands">Netherlands</option>
                                <option value="New Zealand">New Zealand</option>
                                <option value="Nicaragua">Nicaragua</option>
                                <option value="Niger">Niger</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="North Korea">North Korea</option>
                                <option value="North Macedonia">North Macedonia</option>
                                <option value="Norway">Norway</option>
                            </optgroup>

                            <optgroup label="O">
                                <option value="Oman">Oman</option>
                            </optgroup>

                            <optgroup label="P">
                                <option value="Pakistan">Pakistan</option>
                                <option value="Palau">Palau</option>
                                <option value="Palestine State">Palestine State</option>
                                <option value="Panama">Panama</option>
                                <option value="Papua New Guinea">Papua New Guinea</option>
                                <option value="Paraguay">Paraguay</option>
                                <option value="Peru">Peru</option>
                                <option value="Philippines">Philippines</option>
                                <option value="Poland">Poland</option>
                                <option value="Portugal">Portugal</option>
                            </optgroup>

                            <optgroup label="Q">
                                <option value="Qatar">Qatar</option>
                            </optgroup>

                            <optgroup label="R">
                                <option value="Romania">Romania</option>
                                <option value="Russia">Russia</option>
                                <option value="Rwanda">Rwanda</option>
                            </optgroup>

                            <optgroup label="S">
                                <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                <option value="Saint Lucia">Saint Lucia</option>
                                <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                <option value="Samoa">Samoa</option>
                                <option value="San Marino">San Marino</option>
                                <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Serbia">Serbia</option>
                                <option value="Seychelles">Seychelles</option>
                                <option value="Sierra Leone">Sierra Leone</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Slovenia">Slovenia</option>
                                <option value="Solomon Islands">Solomon Islands</option>
                                <option value="Somalia">Somalia</option>
                                <option value="South Africa">South Africa</option>
                                <option value="South Korea">South Korea</option>
                                <option value="South Sudan">South Sudan</option>
                                <option value="Spain">Spain</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Sudan">Sudan</option>
                                <option value="Suriname">Suriname</option>
                                <option value="Sweden">Sweden</option>
                                <option value="Switzerland">Switzerland</option>
                                <option value="Syria">Syria</option>
                            </optgroup>

                            <optgroup label="T">
                                <option value="Tajikistan">Tajikistan</option>
                                <option value="Tanzania">Tanzania</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Timor-Leste">Timor-Leste</option>
                                <option value="Togo">Togo</option>
                                <option value="Tonga">Tonga</option>
                                <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Turkmenistan">Turkmenistan</option>
                                <option value="Tuvalu">Tuvalu</option>
                            </optgroup>

                            <optgroup label="U">
                                <option value="Uganda">Uganda</option>
                                <option value="Ukraine">Ukraine</option>
                                <option value="United Arab Emirates">United Arab Emirates</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="United States of America">United States of America</option>
                                <option value="Uruguay">Uruguay</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                            </optgroup>

                            <optgroup label="V">
                                <option value="Vanuatu">Vanuatu</option>
                                <option value="Venezuela">Venezuela</option>
                                <option value="Vietnam">Vietnam</option>
                            </optgroup>

                            <optgroup label="Y">
                                <option value="Yemen">Yemen</option>
                            </optgroup>

                            <optgroup label="Z">
                                <option value="Zambia">Zambia</option>
                                <option value="Zimbabwe">Zimbabwe</option>
                            </optgroup>
                        </select>
                    </div>
                    </div>
                <div class="submit-btn-container">
                    <button type="submit" class="submit-btn">CONFIRM & PROCEED TO TITLE</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay hidden" id="titleModal">
        <div class="form-card">
            <span class="close-icon" onclick="closeModal('titleModal')">&times;</span>
            <div class="form-title">STEP 2: ADD NEW TITLE</div>
            
            <form method="POST">
                <input type="hidden" name="action" value="add_title">
                
                <input type="hidden" name="pub_id" id="hidden_pub_id">

                <div class="form-grid">
                    <div class="input-group full-width">
                        <label class="text-label">Title Name</label>
                        <input type="text" name="title" class="table-input" required>
                    </div>
                    
                    <div class="input-group">
                        <label class="text-label">Type</label>
                        <select name="type" class="table-input" required>
                            <option value="" disabled selected>Select a Category...</option>
                            
                            <optgroup label="1. General Non-Fiction">
                                <option value="Arts & Recreation">Arts & Recreation</option>
                                <option value="Biographies & Memoirs">Biographies & Memoirs</option>
                                <option value="Business & Economics">Business & Economics</option>
                                <option value="History & Geography">History & Geography</option>
                                <option value="Philosophy & Psychology">Philosophy & Psychology</option>
                                <option value="Religion & Spirituality">Religion & Spirituality</option>
                                <option value="Science & Nature">Science & Nature</option>
                                <option value="Social Sciences">Social Sciences</option>
                                <option value="Technology & Applied Science">Technology & Applied Science</option>
                                <option value="True Crime">True Crime</option>
                            </optgroup>

                            <optgroup label="2. Fiction">
                                <option value="Action & Adventure">Action & Adventure</option>
                                <option value="Classics">Classics</option>
                                <option value="Contemporary Fiction">Contemporary Fiction</option>
                                <option value="Fantasy">Fantasy</option>
                                <option value="Historical Fiction">Historical Fiction</option>
                                <option value="Horror">Horror</option>
                                <option value="Literary Fiction">Literary Fiction</option>
                                <option value="Mystery & Thriller">Mystery & Thriller</option>
                                <option value="Romance">Romance</option>
                                <option value="Science Fiction">Science Fiction</option>
                            </optgroup>

                            <optgroup label="3. Visual & Alternative Formats">
                                <option value="Graphic Novels">Graphic Novels</option>
                                <option value="Manga">Manga</option>
                                <option value="Comic Books">Comic Books</option>
                                <option value="Large Print">Large Print</option>
                                <option value="Audiobooks">Audiobooks</option>
                            </optgroup>

                            <optgroup label="4. Specialized Collections">
                                <option value="Reference">Reference</option>
                                <option value="Periodicals">Periodicals</option>
                                <option value="Government Documents">Government Documents</option>
                                <option value="Special Collections/Archives">Special Collections/Archives</option>
                            </optgroup>

                            <optgroup label="5. Age-Specific Categories">
                                <option value="Children’s">Children’s (Board Books, Picture Books...)</option>
                                <option value="Young Adult">Young Adult (YA)</option>
                                <option value="Adult">Adult</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <label class="text-label">Pub Date</label>
                        <input type="date" name="pubdate" class="table-input" required>
                    </div>

                    <div class="input-group">
                        <label class="text-label">Price</label>
                        <input type="text" name="price" class="table-input">
                    </div>

                    <div class="input-group">
                        <label class="text-label">Advance</label>
                        <input type="text" name="advance" class="table-input">
                    </div>
                    
                    <div class="input-group">
                        <label class="text-label">Royalty</label>
                        <input type="number" name="royalty" class="table-input">
                    </div>

                    <div class="input-group">
                        <label class="text-label">YTD Sales</label>
                        <input type="number" name="ytd_sales" class="table-input">
                    </div>

                    <div class="input-group full-width">
                        <label class="text-label">Notes</label>
                        <input type="text" name="notes" class="table-input">
                    </div>
                </div>
                <div class="submit-btn-container">
                    <button type="submit" class="submit-btn" style="background-color: var(--btn-return-color);">FINISH & SAVE TITLE</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).style.display = 'none';
        }

        // Handles Step 1 (Publisher) submission via AJAX
        function handlePublisherSubmit(e) {
            e.preventDefault(); 

            const data = {
                pub_name: document.getElementById('pub_name').value,
                city: document.getElementById('city').value,
                state: document.getElementById('state').value,
                country: document.getElementById('country').value
            };

            // Call the AJAX endpoint at top of file
            fetch('?ajax_add_publisher=1', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    // 1. Close Publisher Modal
                    closeModal('pubModal');
                    
                    // 2. Set the generated (or found) PUB_ID in the Title Form
                    document.getElementById('hidden_pub_id').value = result.pub_id;
                    
                    // 3. Open Title Modal
                    openModal('titleModal');
                } else {
                    alert('Error adding publisher: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred. Check console.');
            });
        }
    </script>

    </body>
    </html>