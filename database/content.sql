-- Published source content, reviewed 2026-09-19. No customer enquiries or invented prices.

USE uc_properties;

SET NAMES utf8mb4;

INSERT INTO settings (setting_key,setting_value) VALUES
('company','UC Properties Limited'),
('phone','+2348065741674'),
('phone_display','+234 806 574 1674'),
('whatsapp','2348065741674'),
('email','info@ucpropertiesltd.com'),
('address','Suite 36C/38D, Second Floor, Anon Plaza, Gudu, Abuja'),
('company_intro','UC Properties Limited helps individuals and businesses explore property opportunities in Abuja and beyond. Our services bring together real estate, investment guidance, and project management, with a focus on integrity, transparency, and each client’s needs.');

INSERT INTO locations (id,name,description,cover_image) VALUES
(1,'Idu (Gousa)','Idu (Gousa) District, Abuja.','assets/images/villa-valore-estate.jpg'),
(2,'Sheretti, Kabusa','Sheretti District, Kabusa, by Sunnyvale Express Road, Abuja.','assets/images/addis-city-estate.png'),
(3,'Kuje','After Hand Maid’s Girls Secondary School, Kuje.','assets/images/down-town-golf-estate.png');

INSERT INTO estates (id,location_id,name,slug,summary,description,address,amenities,documentation,cover_image,featured,published) VALUES
(1,1,'Villa Valoré','villa-valore','Explore land options in Idu (Gousa), with a range of published plot sizes.','Villa Valoré is listed by UC Properties in Idu (Gousa) District, Abuja. Browse the published plot options and proposed designs, then speak with the team about current availability and inspection arrangements.','Idu (Gousa) District, Abuja.','Green garden
Smart home features
Automated gate
Solar energy
Borehole','Ask the team for the current documentation pack, title information, and applicable charges for your selected plot.','assets/images/villa-valore-estate.jpg',1,1),
(2,2,'Addis City','addis-city','Find your starting point in Sheretti District, Kabusa, Abuja.','Addis City is listed in Sheretti District, Kabusa, by Sunnyvale Express Road, Abuja. Published options range from 150 m² to 750 m². Contact UC Properties to discuss the specific plot, proposed design, and current terms.','Sheretti District, Kabusa, by Sunnyvale Express Road, Abuja.','Automated gate
Green garden
CCTV
Smart home features
Solar energy','Ask for the documentation relating to the exact plot you are considering. The team will explain what is supplied and any separate fees.','assets/images/addis-city-estate.png',1,1),
(3,3,'Down Town Golf Resort','down-town-golf-resort','Discover the published estate and plot options in Kuje.','Down Town Golf Resort is listed after Hand Maid’s Girls Secondary School in Kuje. Published plot options range from 250 m² to 1,000 m². Speak to the team to confirm access directions, available plots, and the latest development position.','After Hand Maid’s Girls Secondary School, Kuje.','Golf resort
Solar energy
Security
Green spaces','Contact the team for plot-specific documentation, title details, and the current schedule of charges.','assets/images/down-town-golf-estate.png',1,1);

INSERT INTO property_options (id,estate_id,name,size_sqm,price_includes,published) VALUES
(1,1,'150 m² plot option',150,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(2,1,'250 m² plot option',250,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(3,1,'300 m² plot option',300,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(4,1,'350 m² plot option',350,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(5,1,'400 m² plot option',400,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(6,1,'500 m² plot option',500,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(7,2,'150 m² plot option',150,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(8,2,'250 m² plot option',250,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(9,2,'350 m² plot option',350,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(10,2,'400 m² plot option',400,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(11,2,'750 m² plot option',750,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(12,3,'250 m² plot option',250,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(13,3,'350 m² plot option',350,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(14,3,'450 m² plot option',450,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(15,3,'550 m² plot option',550,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1),
(16,3,'1,000 m² plot option',1000,'Land option. Construction, documentation charges, and other fees must be confirmed separately.',1);

INSERT INTO prototypes (id,name,slug,bedrooms,bathrooms,description,specifications,offer_includes,cover_image,published) VALUES
(1,'5-bedroom fully detached home','five-bedroom-detached',5,6,'A proposed fully detached building design published for Villa Valoré. Use the rendering to begin a conversation about your preferred layout and requirements.','Published design: 5 bedrooms and 6 bathrooms. Full construction specifications and floor plans are available from the team when confirmed.','This is a proposed building design, not a completed home or a construction quotation. Land and construction costs must be confirmed separately.','assets/images/villa-valore-5bed-detached.jpg',1),
(2,'5-bedroom penthouse with BQ','five-bedroom-penthouse-bq',5,6,'A proposed fully detached penthouse with a boys’ quarters, published for Down Town Golf Resort. Ask the team to explain the room schedule and what the BQ includes.','Published design: 5 bedrooms and 6 bathrooms. BQ layout and detailed specifications require confirmation.','Illustrative building design only. The published reference does not establish an inclusive land-and-construction price. Request a current written breakdown.','assets/images/down-town-golf-5bed-penthouse.png',1);

INSERT INTO compatibility (option_id,prototype_id,approved,notes) VALUES
(6,1,0,'Source associates this design with a 500 m² Villa Valoré option. Staff must verify and approve suitability before public use.'),
(15,2,0,'Source associates this design with a 550 m² Kuje option. Staff must verify and approve suitability before public use.');

INSERT INTO faqs (question,answer,sort_order,published) VALUES
('Where are your estates located?','Our published estates are Villa Valoré in Idu (Gousa), Addis City in Sheretti District, Kabusa, and Down Town Golf Resort in Kuje. Contact the team for precise access directions before visiting.',1,1),
('How can I book an inspection?','Choose an estate and complete the inspection request form, or call or WhatsApp {{phone}}. We will contact you to confirm the date and arrangements. Sending a request does not confirm an appointment.',2,1),
('Can I pay in instalments?','Payment arrangements depend on the selected estate and offer. Ask the team for the current instalment total, deposit, duration, payment schedule, and any extra charges before making a commitment.',3,1),
('What does the displayed price include?','Only confirmed pricing is displayed. Check the “What the price includes” information for each option. A land price does not automatically include construction. Where information is incomplete, ask for a current written quotation.',4,1),
('Are additional charges payable?','Charges can vary by estate and transaction. Ask the team for a full written breakdown, including documentation, development, service, and any other applicable charges.',5,1),
('What documents are provided?','Documents depend on the estate and the specific plot or property. Ask UC Properties for the current documentation pack and title information, and arrange independent review before proceeding.',6,1),
('Can I enquire from outside Nigeria?','Yes. You can send an enquiry through the website or WhatsApp from outside Nigeria. Tell the team your location and preferred contact time, and ask about available inspection arrangements.',7,1),
('How do I contact the company?','Call or WhatsApp {{phone}}, email {{email}}, or use the contact form. Our published office address is {{address}}.',8,1),
('Are the building images completed homes?','Images labelled architectural renderings illustrate proposed designs. They do not show completed construction. Ask the team for current site photographs and the latest development information.',9,1),
('Will every design fit my plot?','No. A design is shown as compatible only after staff explicitly approve its association with a specific estate and plot option. Plot size alone does not establish suitability.',10,1);
