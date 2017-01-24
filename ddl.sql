CREATE TABLE peer_reviews (
	id INT IDENTITY PRIMARY KEY,
	post_url VARCHAR(255) NOT NULL UNIQUE,
	score TINYINT CHECK (score < 101)
);